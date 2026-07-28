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
    'mage/translate',
    'mage/validation'
], function ($, ucClient) {
    'use strict';

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
    //   New card  => transient_token = <UC transient-token JWT>, card identity empty/absent.
    // This form has no stored-card select; the card-identity hidden field (frontend "id" / admin
    // "card_id", the card hash) is empty on add-card and set on edit-card, and is posted as-is —
    // the Save controllers key add-vs-edit(replace) on it.
    // ---------------------------------------------------------------------------------------------

    $.widget('mage.cybersourcePaymentInfoForm', {
        options: {
            captureContextUrl: null,
            tokenSelector: '[name="payment[transient_token]"]',
            fieldPrefix: '#'
        },

        _create: function () {
            this.element.find('#submit-address').on('click', this.showPayment.bind(this));
            this.element.find('#edit-address').on('click', this.showAddress.bind(this));
            this.dropinMounted = false;
            this._captureXhr = null;

            // Only a context timer here: handleTransientToken() submits the form the moment a token is
            // captured, so unlike the checkout renderer this widget never holds one long enough to lapse.
            this._contextTimer = null;
            this._healthTimer = null;

            // Consecutive dead mounts. Deliberately NOT reset by teardownDropin(), or the health-check
            // retry would clear its own counter on every cycle and never trip; showPayment() resets it,
            // so a customer who backs out to the address step and confirms again gets a clean budget.
            this._mountFailures = 0;

            // Invalidates in-flight mount cycles; see loadClientLibrary(). The sticky dropinMounted flag
            // already blocks concurrent mounts here, but a teardown mid-cycle (address edit) clears it
            // and lets a new cycle start while the old one is still loading.
            this._mountGeneration = 0;
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
            ['_contextTimer', '_healthTimer'].forEach(function (handle) {
                if (this[handle]) {
                    clearTimeout(this[handle]);
                    this[handle] = null;
                }
            }.bind(this));
        },

        showAddress: function () {
            this.element.find('.address').show();
            this.element.find('.payment').hide();

            // Returning to edit the address invalidates the mounted drop-in/token; tear it down so a
            // fresh capture context (with the updated billing address) is requested on the next mount.
            this.teardownDropin();
        },

        /**
         * Tear down a mounted/in-flight drop-in: cancel the TTL timer, abort the request, clear the
         * captured token and empty the containers so the next mount starts clean.
         */
        teardownDropin: function () {
            this.clearTimers();

            if (this._captureXhr) {
                this._captureXhr.abort();
                this._captureXhr = null;
            }

            this.element.find(this.options.tokenSelector).val('');
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

        showPayment: function () {
            this.element.validation();
            if (this.element.validation('isValid') !== true) {
                return;
            }

            this.renderAddress();

            this.element.find('.address').hide();
            this.element.find('.payment').show();

            this.fixScroll();

            // Deliberate (re-)entry into the payment step; clear the failure budget so a past dead mount
            // does not dead-end a customer who backed out and is explicitly trying again.
            this._mountFailures = 0;

            this.mountDropin();
        },

        renderAddress: function () {
            var address = $(this.options.fieldPrefix + 'firstname').val() + ' ';
            address += $(this.options.fieldPrefix + 'lastname').val() + '<br>';
            address += $(this.options.fieldPrefix + 'company').val()
                       ? $(this.options.fieldPrefix + 'company').val() + '<br>'
                       : '';
            address += $(this.options.fieldPrefix + 'street').val() + '<br>';
            address += $(this.options.fieldPrefix + 'street_2').val()
                       ? $(this.options.fieldPrefix + 'street_2').val() + '<br>'
                       : '';
            address += $(this.options.fieldPrefix + 'city').val() + ', ';
            address += $(this.options.fieldPrefix + 'region-id option:selected').text()
                       ? $(this.options.fieldPrefix + 'region-id option:selected').text() + ' '
                       : $(this.options.fieldPrefix + 'region').val() + ' ';
            address += $(this.options.fieldPrefix + 'zip').val() + '<br>';
            address += $(this.options.fieldPrefix + 'country option:selected').text() + '<br>';
            address += $(this.options.fieldPrefix + 'telephone').val()
                       ? $(this.options.fieldPrefix + 'telephone').val()
                       : '';

            this.element.find('address').html(address);
        },

        fixScroll: function () {
            var topPosition = $('fieldset.payment:first').position().top;

            if (topPosition < window.scrollY) {
                window.scrollTo(0, topPosition);
            }
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

            this.element.find('.unified-checkout-screen').trigger('processStart');

            this._captureXhr = $.post({
                url: this.options.captureContextUrl,
                dataType: 'json',
                data: this.element.serialize(),
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
         * Re-request a fresh capture context + drop-in before the capture context lapses, so a long
         * customer/admin session does not tokenize against a dead context (M5).
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
         * (children present, not visible-at-zero-height — the drop-in lives in the hidden '.payment'
         * pane until showPayment() reveals it) is delegated to ucClient.
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
         * rather than leaving the customer staring at an empty payment box with no explanation.
         */
        checkMountHealth: function () {
            this._healthTimer = null;

            if (!this.element.find('.unified-checkout-screen').is(':visible')) {
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
         * Schedule a silent re-mount for when the capture context lapses. Nothing the customer entered
         * survives a lapsed context anyway, and no token can be outstanding here (capture submits the
         * form immediately), so this needs no announcement.
         */
        scheduleContextRefresh: function (captureContext) {
            if (this._contextTimer) {
                clearTimeout(this._contextTimer);
            }

            this._contextTimer = setTimeout(
                this.remountDropin.bind(this),
                ucClient.getRefreshDelay(captureContext)
            );
        },

        /**
         * Decode the capture-context JWT, inject UC.js (with SRI), then mount the drop-in.
         *
         * The capture context is threaded through as an argument rather than stashed on the instance, so
         * a late response from a superseded cycle cannot overwrite the context the drop-in is bound to.
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
         * Stash the transient token in the hidden form input, then submit the form.
         *
         * The form posts to the TokenBase paymentinfo save controller, which exchanges the transient
         * token for a vault card (via Card::beforeSave) and performs its own redirect + success/error
         * message. We always submit here. The card-identity field (frontend "id" / admin "card_id",
         * the card hash) is posted AS-IS: empty on add-card, set on edit-card — the Save controllers
         * key the add-vs-edit(replace) decision on it, so it must never be stripped (stripping it
         * made every edit save a new card instead of replacing the one being edited).
         */
        handleTransientToken: function (transientTokenJwt) {
            if (!transientTokenJwt) {
                return;
            }

            this.element.find(this.options.tokenSelector).val(transientTokenJwt);

            // Card entry is done and the form is about to leave; disarm the timers so nothing fires
            // against a page that is on its way out. The submit below snapshots the fields regardless,
            // but leaving live timers behind that could empty the containers is needless.
            this.clearTimers();

            this.element.submit();
        },

        handleAjaxError: function (jqXHR, status, error) {
            // Aborts are intentional teardown (address edit / re-mount), not failures.
            if (status === 'abort') {
                return;
            }

            this.element.find('.unified-checkout-screen').trigger('processStop');
            this.dropinMounted = false;
            this._captureXhr = null;

            var message = $.mage.__('A server error occurred. Please try again.');

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

            ucClient.showError(message);
        }
    });

    return $.mage.cybersourcePaymentInfoForm;
});
