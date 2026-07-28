/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
 */

/*jshint jquery:true*/
define([
    'jquery',
    'ParadoxLabs_CyberSource/js/unified-checkout-client',
    'mage/translate'
], function ($, ucClient) {
    'use strict';

    // Placeholder card id used to flag a freshly tokenized (not-yet-vaulted) card to the form.
    var NEW_CARD_ID = 'unified_checkout_new';
    // How long after a mount is kicked off to verify the drop-in actually painted. The UC iframe
    // loads asynchronously and exposes no documented ready event, so this is a wall-clock check.
    var MOUNT_HEALTH_CHECK_MS = 3000;
    // Outer bound on a whole mount cycle (request -> UC.js load -> mount). The library load is the one
    // leg that can stall without ever calling back — neither onReady nor onError — which would leave
    // dropinMounted stuck true and block every future attempt. Arming the health check when the cycle
    // opens means that stall is caught and retried like any other dead mount.
    var CYCLE_TIMEOUT_MS = 45000;
    // Consecutive dead-mount cap. Bounds the health-check retry so a persistently broken mount stops
    // rather than looping on signed capture-context calls; the customer is told once when it trips.
    var MAX_MOUNT_FAILURES = 3;

    // ---------------------------------------------------------------------------------------------
    // Iter-3 additional_data contract (shared with the KO renderer):
    //   New card  => transient_token = <UC transient-token JWT>, card_id empty/absent.
    //   Stored card => transient_token empty, card_id = <vault hash>.
    // The synthetic NEW_CARD_ID placeholder is a UI affordance only; addAndSelectCard() clears the
    // submitted card_id value so the placeholder never reaches the server as a real card_id.
    // ---------------------------------------------------------------------------------------------

    $.widget('mage.cybersourceLegacyForm', {
        options: {
            captureContextUrl: null,
            fingerprintUrl: null,
            tokenSelector: '[name="payment[transient_token]"]',
            cardSelector: '[name="payment[card_id]"]'
        },

        _create: function () {
            this.element.on('change', this.options.cardSelector, this.handleCardSelectChange.bind(this));
            this.dropinMounted = false;
            this._captureXhr = null;

            // Expiry timers, deliberately split: a lapsed capture context can be swapped silently
            // (nothing captured yet), whereas a lapsed transient token discards card entry the customer
            // already completed and must be announced. A single mount-armed timer conflated the two and
            // silently wiped the form out from under them.
            this._contextTimer = null;
            this._tokenTimer = null;
            this._healthTimer = null;

            // Consecutive dead mounts. Deliberately NOT reset by teardownDropin(), or the health-check
            // retry would clear its own counter on every cycle and never trip; only an explicit
            // "Add new card" selection resets it, so a deliberate retry still gets a clean budget.
            this._mountFailures = 0;

            // Invalidates in-flight mount cycles; see loadClientLibrary(). The sticky dropinMounted flag
            // already blocks concurrent mounts here, but a teardown mid-cycle (stored-card switch, token
            // expiry) clears it and lets a new cycle start while the old one is still loading — so the
            // old cycle's callbacks must be discardable rather than left to clobber the new context.
            this._mountGeneration = 0;

            this.initFingerprint();
            this.handleCardSelectChange();
        },

        _destroy: function () {
            this.clearTimers();

            // Aborting the xhr only stops a cycle still on its ajax leg; one already past it would
            // otherwise mount into the DOM this widget is being torn down from.
            this._mountGeneration++;

            if (this._captureXhr) {
                this._captureXhr.abort();
                this._captureXhr = null;
            }
        },

        /**
         * Clear every armed timer.
         */
        clearTimers: function () {
            ['_contextTimer', '_tokenTimer', '_healthTimer'].forEach(function (handle) {
                if (this[handle]) {
                    clearTimeout(this[handle]);
                    this[handle] = null;
                }
            }.bind(this));
        },

        handleCardSelectChange: function () {
            var select = this.element.find(this.options.cardSelector);
            var selectedOption = this.element.find(this.options.cardSelector + ' option:selected');

            // A real stored card is selected (non-empty value that is not the captured-token placeholder).
            if (select.val() !== '' && selectedOption.data('id') !== NEW_CARD_ID) {
                this.element.find('div.cvv').show();
                this.element.find('div.save').hide();
                this.teardownDropin();

                return;
            }

            // The freshly tokenized "new card" placeholder (value cleared, flagged via data-id). The
            // drop-in collected the security code during card entry, so asking for it again here is a
            // pointless second prompt; the field is for stored cards only ("require CCV when using a
            // stored card"), and the token already carries what the server consumes.
            if (selectedOption.data('id') === NEW_CARD_ID) {
                this.element.find('div.cvv').hide();
                this.element.find('div.save').toggle(!!selectedOption.data('new'));

                return;
            }

            // 'Add new card' selected: hide additional fields and (re-)mount the drop-in. This is the
            // deliberate-retry path, so clear the failure budget; a past dead mount must not dead-end
            // a customer who is explicitly asking for the form again.
            this.element.find('div.cvv').hide();
            this.element.find('div.save').hide();

            this._mountFailures = 0;

            this.mountDropin();
        },

        /**
         * Tear down a mounted/in-flight drop-in when switching to a stored card, so returning to
         * 'Add new card' re-requests a fresh capture context instead of dead-ending on the latch.
         */
        teardownDropin: function () {
            this.clearTimers();

            if (this._captureXhr) {
                this._captureXhr.abort();
                this._captureXhr = null;
            }

            this.element.find(this.options.tokenSelector).val('');
            this.element.find(this.options.cardSelector + ' option').filter(function () {
                return $(this).data('id') === NEW_CARD_ID;
            }).remove();
            ucClient.releaseHeightRatchet(this.element.find('.unified-checkout-selection'));
            this.element.find('.unified-checkout-selection').empty();
            this.element.find('.unified-checkout-screen').empty();

            // The discarded Accept() instances also left helper iframes on document.body, out of
            // reach of the container empties above; reap them so re-mounts don't accumulate.
            ucClient.reapOrphanedFrames();

            this.dropinMounted = false;

            // Invalidate any cycle still in flight, so a load or mount started before this teardown
            // cannot land afterwards and mount against a context this widget has already discarded.
            this._mountGeneration++;
        },

        /**
         * Request a capture context and load the UC client library (once).
         */
        mountDropin: function () {
            // Guard against a duplicate-mount race: an in-flight request or an already populated
            // screen container means a mount is pending/done; do not issue a second request (I1).
            if (this.dropinMounted === true
                || this._captureXhr !== null
                || this.isDropinMounted()) {
                return;
            }

            this.dropinMounted = true;

            // Open a mount cycle; every async callback below carries this stamp and drops out if a
            // teardown or a newer cycle has superseded it.
            var generation = ++this._mountGeneration;

            // Bound the whole cycle from here; mountUnifiedCheckout() re-arms this at the shorter paint
            // interval once the mount is actually underway.
            this.scheduleMountHealthCheck(CYCLE_TIMEOUT_MS);

            var screen = this.element.find('.unified-checkout-screen');
            screen.trigger('processStart');

            var payload = {};
            var inputs = this.element.find(':input');
            for (var key = 0; key < inputs.length; key++) {
                if (inputs[key] === undefined
                    || inputs[key] === null
                    || inputs[key].name === undefined
                    || inputs[key].name.length === 0) {
                    continue;
                }

                payload[inputs[key].name] = $(inputs[key]).val();
            }

            this._captureXhr = $.post({
                url: this.options.captureContextUrl,
                dataType: 'json',
                data: payload,
                global: false,
                success: function (data, status, jqXHR) {
                    this.loadClientLibrary(data, status, jqXHR, generation);
                }.bind(this),
                error: this.handleAjaxError.bind(this)
            });

            this._captureXhr.always(function () {
                this._captureXhr = null;
            }.bind(this));

            return this._captureXhr;
        },

        /**
         * Re-request a fresh capture context + drop-in before the transient-token TTL lapses, so a
         * long admin/customer session does not submit a stale token (M5).
         */
        remountDropin: function () {
            this.teardownDropin();
            this.mountDropin();
        },

        /**
         * Whether the drop-in is mounted AND usable.
         *
         * UC renders in two shapes: card-only configurations mount the card form straight into the
         * screen container, but with wallets enabled it mounts a buttonlist into the selection
         * container and leaves the screen EMPTY until "Checkout with card" is clicked. Either
         * container holding healthy content is a live mount; checking only the screen reads the
         * buttonlist shape as dead and the health check would remount forever. Per-container health
         * (children present, not visible-at-zero-height) is delegated to ucClient.
         *
         * @return {Boolean}
         */
        isDropinMounted: function () {
            return ucClient.isContainerHealthy(this.element.find('.unified-checkout-selection'))
                || ucClient.isContainerHealthy(this.element.find('.unified-checkout-screen'));
        },

        /**
         * Schedule the mount health check.
         *
         * @param {Number} delay - CYCLE_TIMEOUT_MS when arming at cycle open (the library load can stall
         *                         without ever calling back), MOUNT_HEALTH_CHECK_MS once the mount is
         *                         underway and only the paint is outstanding.
         */
        scheduleMountHealthCheck: function (delay) {
            if (this._healthTimer) {
                clearTimeout(this._healthTimer);
            }

            this._healthTimer = setTimeout(this.checkMountHealth.bind(this), delay);
        },

        /**
         * Recover from a mount that attached but never became usable (the 0-height iframe case).
         *
         * This is also the only place a mount is confirmed to have actually worked, so it owns clearing
         * the failure budget. Each dead mount counts against that budget, and exhausting it says so
         * rather than leaving the customer staring at an empty payment box with no explanation — the
         * stored-card-switch recovery is not available to someone with no stored cards.
         */
        checkMountHealth: function () {
            this._healthTimer = null;

            if (this.element.find(this.options.tokenSelector).val()
                || !this.element.find('.unified-checkout-screen').is(':visible')) {
                return;
            }

            if (this.isDropinMounted()) {
                this._mountFailures = 0;

                return;
            }

            this._mountFailures++;

            if (this._mountFailures >= MAX_MOUNT_FAILURES) {
                ucClient.showError(
                    $.mage.__('The payment form could not be loaded. Please reload the page and try again.')
                );

                return;
            }

            this.remountDropin();
        },

        /**
         * Decode the capture-context JWT, inject UC.js (with SRI), then mount the drop-in.
         *
         * The capture context is threaded through as an argument rather than stashed on the instance: it
         * carries the capture mandate's amount, and a shared property would let a late response from a
         * superseded cycle overwrite the context the mounted drop-in is bound to.
         *
         * @param {Object} data
         * @param {String} status
         * @param {Object} jqXHR
         * @param {Number} generation
         */
        loadClientLibrary: function (data, status, jqXHR, generation) {
            if (!this.isCurrentGeneration(generation)) {
                return;
            }

            if (!data || !data.captureContext) {
                return this.handleAjaxError(jqXHR, status, data);
            }

            ucClient.loadClientLibrary(
                data.captureContext,
                this.mountUnifiedCheckout.bind(this, data.captureContext, generation),
                function (message) {
                    // A superseded cycle's failure is not this cycle's failure: handleAjaxError clears
                    // dropinMounted, which is the in-flight guard here, so a stale error would let a
                    // second mount race the live one. RequireJS de-dupes loads of the same UC.js URL,
                    // so overlapping cycles share one load and its failure reaches every callback.
                    if (!this.isCurrentGeneration(generation)) {
                        return;
                    }

                    this.handleAjaxError(null, 'error', message);
                }.bind(this)
            );
        },

        /**
         * Whether an async callback belongs to the current mount cycle.
         *
         * @param {Number} generation
         * @return {Boolean}
         */
        isCurrentGeneration: function (generation) {
            return generation === this._mountGeneration;
        },

        /**
         * Mount the UC drop-in via the Accept global into the embedded containers.
         *
         * @param {String} captureContext
         * @param {Number} generation
         */
        mountUnifiedCheckout: function (captureContext, generation) {
            if (!this.isCurrentGeneration(generation)) {
                return;
            }

            if (!ucClient.isAvailable()) {
                return this.handleAjaxError(null, 'error', 'Payment library unavailable');
            }

            var selection = this.element.find('.unified-checkout-selection').attr('id');
            var screen = this.element.find('.unified-checkout-screen').attr('id');

            ucClient.mountUnifiedPayments(captureContext, '#' + selection, '#' + screen)
                .then(function (transientTokenJwt) {
                    // A token from a superseded mount belongs to a dead context; never accept it.
                    if (!this.isCurrentGeneration(generation)) {
                        return;
                    }

                    this.handleTransientToken(transientTokenJwt);
                }.bind(this))
                .catch(function (error) {
                    // As above: a superseded mount's rejection must not fail the live cycle.
                    if (!this.isCurrentGeneration(generation)) {
                        return;
                    }

                    this.handleAjaxError(null, 'error', error && error.message ? error.message : null);
                }.bind(this));

            this.element.find('.unified-checkout-screen').trigger('processStop');

            this.scheduleContextRefresh(captureContext);
            this.scheduleMountHealthCheck(MOUNT_HEALTH_CHECK_MS);
        },

        /**
         * Schedule a silent re-mount for when the capture context lapses. Skipped once a token is
         * captured: the context is spent by then and the token timer owns expiry.
         */
        scheduleContextRefresh: function (captureContext) {
            if (this._contextTimer) {
                clearTimeout(this._contextTimer);
            }

            this._contextTimer = setTimeout(function () {
                this._contextTimer = null;

                if (this.element.find(this.options.tokenSelector).val()) {
                    return;
                }

                this.remountDropin();
            }.bind(this), ucClient.getRefreshDelay(captureContext));
        },

        /**
         * Schedule expiry of a captured transient token. Unlike a context refresh this discards card
         * entry the customer already completed, so it is always announced rather than silently wiping
         * the form; the server would reject the stale token anyway.
         *
         * @param {String} transientTokenJwt
         */
        scheduleTokenExpiry: function (transientTokenJwt) {
            if (this._tokenTimer) {
                clearTimeout(this._tokenTimer);
            }

            this._tokenTimer = setTimeout(function () {
                this._tokenTimer = null;

                this.remountDropin();
                this.handleCardSelectChange();

                ucClient.showError(
                    $.mage.__('Your payment session expired. Please re-enter your card details.')
                );
            }.bind(this), ucClient.getRefreshDelay(transientTokenJwt));
        },

        /**
         * Stash the transient token in the hidden form input and add/select a card so the form submits.
         */
        handleTransientToken: function (transientTokenJwt) {
            if (!transientTokenJwt) {
                return;
            }

            this.element.find(this.options.tokenSelector).val(transientTokenJwt);

            // Card entry is done: the capture context is spent and a pending health check is moot; the
            // token's own lifetime takes over from here.
            if (this._contextTimer) {
                clearTimeout(this._contextTimer);
                this._contextTimer = null;
            }

            if (this._healthTimer) {
                clearTimeout(this._healthTimer);
                this._healthTimer = null;
            }

            this.scheduleTokenExpiry(transientTokenJwt);

            // Decode display metadata from the token so the synthetic option reads "Visa ****1111"
            // instead of a bare "New Card" (KO renderer parity); null on any malformed token.
            var metadata = ucClient.getCardMetadata(transientTokenJwt);

            this.addAndSelectCard({
                id: NEW_CARD_ID,
                label: this.getNewCardLabel(metadata),
                new: true,
                type: metadata && metadata.type ? metadata.type : '',
                cc_bin: metadata && metadata.bin ? metadata.bin : '',
                cc_last4: metadata && metadata.last4 ? metadata.last4 : ''
            });
        },

        /**
         * Display label for a freshly tokenized card: "Visa ****1111" when the token metadata
         * decoded, the wallet name for a wallet token, generic "New Card" otherwise.
         *
         * @param {Object|null} metadata - ucClient.getCardMetadata() result
         * @return {String}
         */
        getNewCardLabel: function (metadata) {
            if (metadata && metadata.label && metadata.last4) {
                return $.mage.__('%1 ****%2')
                    .replace('%1', metadata.label)
                    .replace('%2', metadata.last4);
            }

            if (metadata && ucClient.getWalletLabel(metadata.paymentType)) {
                return ucClient.getWalletLabel(metadata.paymentType);
            }

            return $.mage.__('New Card');
        },

        handleAjaxError: function (jqXHR, status, error) {
            // Aborts are intentional teardown (stored-card switch / re-mount), not failures.
            if (status === 'abort') {
                return;
            }

            var screen = this.element.find('.unified-checkout-screen');
            var message = $.mage.__('A server error occurred. Please try again.');

            screen.trigger('processStop');
            this.dropinMounted = false;
            this._captureXhr = null;

            if (typeof error === 'string' && error.length > 0) {
                message = error;
            }

            try {
                if (jqXHR && jqXHR.responseText) {
                    var responseJson = JSON.parse(jqXHR.responseText);
                    if (responseJson.message !== undefined) {
                        message = responseJson.message;
                    }
                }
            } catch (e) {
                // responseText was not JSON; keep the default/passed message.
            }

            if (screen.siblings('.message').length > 0) {
                screen.siblings('.message').text(message).show();
                screen.hide();

                return;
            }

            ucClient.showError(message);
        },

        addAndSelectCard: function (card) {
            // The option carries an EMPTY value so the synthetic placeholder is never submitted as a
            // real card_id (Iter-3 contract); the new-card state is tracked via data-id instead. The
            // transient_token already holds the captured JWT, which is what the server consumes.
            var option = $('<option>');
            option.val('')
                .text(card.label)
                .data('id', card.id)
                .data('new', card.new)
                .data('cc_bin', card.cc_bin)
                .data('cc_last4', card.cc_last4)
                .data('type', card.type);

            var select = this.element.find(this.options.cardSelector);
            select.find('option').prop('selected', false);
            select.append(option);
            option.prop('selected', true);
            select.trigger('change');
        },

        /**
         * Inject the CyberSource Decision Manager (online-metrix) device-fingerprint tag.
         *
         * Legacy parity: the SA/SOAP widget loaded this so DM could collect the device signal keyed on the
         * per-quote session id. Same null/length guard as the legacy widget, plus a de-dupe by URL so a
         * re-init does not append a second identical tag.
         */
        initFingerprint: function () {
            if (this.options.fingerprintUrl === null
                || this.options.fingerprintUrl.length <= 1) {
                return;
            }

            if (document.querySelector('script[data-cybs-fingerprint="' + this.options.fingerprintUrl + '"]')) {
                return;
            }

            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = this.options.fingerprintUrl;
            script.setAttribute('data-cybs-fingerprint', this.options.fingerprintUrl);
            document.head.appendChild(script);
        }
    });

    return $.mage.cybersourceLegacyForm;
});
