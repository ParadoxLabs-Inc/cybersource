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

define(
    [
        'ko',
        'jquery',
        'underscore',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc',
        'Magento_Checkout/js/model/quote',
        'ParadoxLabs_CyberSource/js/unified-checkout-client',
        'mage/translate'
    ],
    function (ko, $, _, Component, quote, ucClient) {
        'use strict';
        var config = window.checkoutConfig.payment.paradoxlabs_cybersource;
        // How long after a mount is kicked off to verify the drop-in actually painted. The UC iframe
        // loads asynchronously and exposes no documented ready event, so this is a wall-clock check.
        var MOUNT_HEALTH_CHECK_MS = 3000;
        // Outer bound on a whole mount cycle (request -> UC.js load -> mount). The library load is the
        // one leg that can stall without ever calling back — neither onReady nor onError — which would
        // leave the in-flight guard set and every future mount attempt blocked. Arming the health check
        // when the cycle opens means that stall is caught and retried like any other dead mount.
        var CYCLE_TIMEOUT_MS = 45000;
        // Placeholder card id used to flag a freshly tokenized (not-yet-vaulted) card to the place-order UI.
        var NEW_CARD_ID = 'unified_checkout_new';
        // Consecutive-failure cap on mount attempts. Quote observables (billing address, payment method)
        // notify on many checkout actions; without a latch a persistent failure (e.g. UC.js blocked by
        // CSP) turns every notification into a fresh signed capture-context API call, indefinitely.
        var MAX_MOUNT_FAILURES = 3;

        // ---------------------------------------------------------------------------------------------
        // Iter-3 additional_data contract (getData()):
        //   New card  => transient_token = <UC transient-token JWT>, card_id = null (empty).
        //   Stored card => transient_token = null, card_id = <vault hash>.
        // The synthetic NEW_CARD_ID placeholder is normalized to null here; it never reaches the
        // server as a real card_id. Exactly one of {transient_token, card_id} is populated per submit.
        // ---------------------------------------------------------------------------------------------
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_CyberSource/payment/unified-checkout',
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : [],
                logoImage: config ? config.logoImage : false,
                transientToken: null,
                lastGrandTotal: null
            },
            initVars: function () {
                this.canSaveCard = config ? config.canSaveCard : false;
                this.forceSaveCard = config ? config.forceSaveCard : false;
                this.defaultSaveCard = config ? config.defaultSaveCard : false;
                this.requireCcv = config ? config.requireCcv : false;
            },
            initObservable: function () {
                this.initVars();
                this._super()
                    .observe([
                        'transientToken'
                    ]);

                this.storedCards = ko.observableArray(config.storedCards);

                // In-flight capture-context jqXHR; tracked so rapid total/card changes can abort a
                // stale request instead of double-mounting.
                this._captureXhr = null;

                // Mount cycle bookkeeping. A cycle spans request -> library load -> mount -> paint, all
                // async, and the jqXHR handle only covers the first leg: it is nulled the moment the ajax
                // call settles, leaving the load/mount/paint legs unguarded. A quote notification landing
                // in that window used to slip every guard and start a second concurrent cycle, whose
                // response would then clobber the first's capture context — and the context carries the
                // capture mandate's amount, so the drop-in could end up bound to a different total than
                // the order being placed. _activeGeneration marks a cycle in flight for its whole life;
                // _mountGeneration invalidates superseded cycles so their late callbacks are discarded.
                this._mountGeneration = 0;
                this._activeGeneration = null;

                // Expiry timers, deliberately split. The capture context and the transient token have
                // separate lifetimes and separate consequences: a lapsed context only matters while the
                // customer has yet to tokenize (silent re-mount, nothing to lose), whereas a lapsed token
                // means discarding card entry the customer already completed, which must be announced.
                // A single mount-armed timer conflated the two and silently wiped in-progress checkouts.
                this._contextTimer = null;
                this._tokenTimer = null;
                this._healthTimer = null;

                // Consecutive mount/load failures; latches maybeMountDropin() at MAX_MOUNT_FAILURES.
                this._mountFailures = 0;

                // Last seen selectedCard value. The TokenBase cc.js base fires
                // selectedCard.notifySubscribers() on a 100ms interval (form-validation UX), so the
                // subscription receives constant no-change notifications; the handler must only react
                // to actual transitions or every tick becomes a mount attempt / latch reset.
                this._lastSelectedCard = this.selectedCard();

                // Capture subscription handles so dispose() can tear them down; on checkout region
                // re-render the component is recreated and these would otherwise accumulate (N x handlers).
                this._subscriptions = [
                    quote.billingAddress.subscribe(this.maybeMountDropin.bind(this)),
                    quote.paymentMethod.subscribe(this.maybeMountDropin.bind(this)),
                    this.selectedCard.subscribe(this.handleSelectedCardChange.bind(this)),
                    // Re-request the capture context whenever the grand total changes, so the amount in
                    // the capture mandate stays in sync with the order being placed.
                    quote.totals.subscribe(this.handleTotalChange.bind(this))
                ];

                this.showDropin = ko.computed(function () {
                    return (this.selectedCard() === null || this.selectedCard() === undefined)
                           && quote.billingAddress() !== null;
                }, this);

                this.showSaveOption = ko.computed(function () {
                    if (this.canSaveCard !== true
                        || this.selectedCard() === null
                        || this.selectedCard() === undefined) {
                        return false;
                    }

                    var cards = this.storedCards();
                    for (var key in cards) {
                        if (cards[key].id === this.selectedCard()) {
                            return cards[key].new;
                        }
                    }

                    return false;
                }, this);

                this.useVault = ko.computed(function () {
                    return this.storedCards().length > 0;
                }, this);

                this.loadFingerprint();

                return this;
            },

            /**
             * Inject the CyberSource Decision Manager (online-metrix) device-fingerprint tag.
             *
             * Legacy parity: the SA/SOAP renderer loaded this so DM could collect the device signal keyed on
             * the per-quote session id. Guarded against duplicate injection because the checkout region can
             * re-render this component; the tag only needs to load once per page.
             */
            loadFingerprint: function () {
                if (config.fingerprintUrl === undefined
                    || config.fingerprintUrl === null
                    || config.fingerprintUrl.length <= 1) {
                    return;
                }

                // De-dupe by URL: a re-render must not append a second identical tag.
                if (document.querySelector('script[data-cybs-fingerprint="' + config.fingerprintUrl + '"]')) {
                    return;
                }

                // Bypassing requireJS because this is easy enough and bypasses core .min-ifying.
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = config.fingerprintUrl;
                script.setAttribute('data-cybs-fingerprint', config.fingerprintUrl);
                document.head.appendChild(script);
            },

            /**
             * Mount the Unified Checkout drop-in when we are the active method, have a billing address,
             * are adding a new card, and the drop-in is not already mounted/in-flight.
             *
             * The guard keys on actual mount health rather than a sticky boolean, so a stored -> new card
             * transition reliably re-mounts (a sticky latch would dead-end).
             *
             * The failure latch stops the quote subscriptions from re-requesting forever when the
             * mount cannot succeed (e.g. UC.js blocked); an explicit "Add new card" re-selection
             * resets it (handleSelectedCardChange) so a transient outage stays recoverable.
             */
            maybeMountDropin: function () {
                var screen = $('#' + this.getCode() + '_uc_screen');

                if (quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || this.selectedCard()
                    || quote.billingAddress() === null
                    || screen.length === 0
                    || this._activeGeneration !== null
                    || this._mountFailures >= MAX_MOUNT_FAILURES
                    || this.isDropinMounted()) {
                    return;
                }

                this.requestCaptureContext();
            },

            /**
             * Whether the drop-in is mounted AND usable.
             *
             * Container emptiness alone cannot tell a healthy mount from a dead one: the UC iframe can
             * attach at 0x0 (FIT_WINDOW never applied), leaving the customer with no visible payment UI
             * while a children-only guard reports "mounted" and refuses to re-mount forever. A visible
             * container at zero height is therefore treated as unmounted. The visibility qualifier
             * matters — showDropin() hides the block via a visible binding, and a hidden-but-healthy
             * mount legitimately measures zero.
             *
             * @return {Boolean}
             */
            isDropinMounted: function () {
                var screen = $('#' + this.getCode() + '_uc_screen');

                if (screen.children().length === 0) {
                    return false;
                }

                return !screen.is(':visible') || screen.height() > 0;
            },

            /**
             * React to a card-selection change.
             *
             * - Stored card selected (drop-in hidden): reset captured state + empty the containers so
             *   returning to "Add new card" re-requests a fresh capture context and re-mounts (C1).
             * - "Add new card" selected (empty): attempt a mount.
             * - Synthetic NEW_CARD_ID (a freshly captured token): do nothing, so the capture does not
             *   re-trigger the mount path (I3).
             */
            handleSelectedCardChange: function () {
                var selected = this.selectedCard();

                // Ignore the base class's 100ms notifySubscribers() ticks; act on real changes only.
                if (selected === this._lastSelectedCard) {
                    return;
                }

                this._lastSelectedCard = selected;

                if (selected === NEW_CARD_ID) {
                    return;
                }

                if (selected) {
                    // A real stored card is now selected; abandon any pending/mounted drop-in.
                    this.resetDropinState();

                    return;
                }

                // Explicit return to "Add new card": clear the failure latch so a past transient
                // failure does not dead-end a deliberate retry.
                this._mountFailures = 0;

                // Deliberately re-entering a card after already tokenizing one; drop the captured token
                // and its timer so the abandoned token cannot outlive the entry it came from.
                if (this.transientToken()) {
                    this.resetDropinState();
                }

                this.maybeMountDropin();
            },

            /**
             * Request a capture-context JWT from the frontend endpoint, then load the UC client library.
             *
             * @return {jqXHR}
             */
            requestCaptureContext: function () {
                $('#' + this.getCode() + '_uc_screen').trigger('processStart');
                this.lastGrandTotal = this.getGrandTotal();

                // Open a mount cycle. Every async callback below carries this stamp and drops out if it
                // no longer matches, so a superseded cycle cannot mount or hand back a token.
                var generation = ++this._mountGeneration;
                this._activeGeneration = generation;

                // Bound the whole cycle from here; mountUnifiedCheckout() re-arms this at the shorter
                // paint interval once the mount is actually underway.
                this.scheduleMountHealthCheck(CYCLE_TIMEOUT_MS);

                this._captureXhr = $.post({
                    url: config.captureContextUrl,
                    dataType: 'json',
                    data: this.getCaptureContextParams(),
                    global: false,
                    success: function (data) {
                        this.loadClientLibrary(data, generation);
                    }.bind(this),
                    error: this.handleAjaxError.bind(this)
                });

                // Only the ajax leg is done here; the cycle stays open through load and mount, so this
                // releases the abort handle but deliberately not the in-flight guard.
                this._captureXhr.always(function () {
                    this._captureXhr = null;
                }.bind(this));

                return this._captureXhr;
            },

            /**
             * Whether an async callback belongs to the current mount cycle. A stale callback is one whose
             * cycle was superseded (a new request) or torn down (resetDropinState) while it was in flight.
             *
             * @param {Number} generation
             * @return {Boolean}
             */
            isCurrentGeneration: function (generation) {
                return generation === this._mountGeneration;
            },

            /**
             * Decode the capture-context JWT, inject the UC client library (with SRI), then mount the drop-in.
             *
             * The capture context is threaded through the chain as an argument rather than stashed on the
             * instance: it carries the capture mandate's amount, and a shared property would let a late
             * response from a superseded cycle overwrite the context the mounted drop-in is bound to.
             *
             * @param {Object} data
             * @param {Number} generation
             */
            loadClientLibrary: function (data, generation) {
                if (!this.isCurrentGeneration(generation)) {
                    return;
                }

                if (!data || !data.captureContext) {
                    this.handleAjaxError(null, 'error', 'Missing capture context');

                    return;
                }

                ucClient.loadClientLibrary(
                    data.captureContext,
                    this.mountUnifiedCheckout.bind(this, data.captureContext, generation),
                    function (message) {
                        // A superseded cycle's failure is not this cycle's failure: handleAjaxError
                        // mutates shared state (the in-flight guard, the failure budget), so letting a
                        // stale error through would clobber a live mount. RequireJS de-dupes loads of
                        // the same UC.js URL, so overlapping cycles share one underlying load and its
                        // failure fans out to every waiting callback — this is routine, not exotic.
                        if (!this.isCurrentGeneration(generation)) {
                            return;
                        }

                        this.handleAjaxError(null, 'error', message);
                    }.bind(this)
                );
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
                    this.handleAjaxError(null, 'error', 'Payment library unavailable');

                    return;
                }

                ucClient.mountUnifiedPayments(
                    captureContext,
                    '#' + this.getCode() + '_uc_selection',
                    '#' + this.getCode() + '_uc_screen'
                )
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

                // NB: the latch is deliberately NOT cleared here. A mount being *underway* proves nothing
                // — the 0-height failure mode reaches exactly this point every time — and clearing on
                // attempt would let checkMountHealth()'s increment be zeroed before the next check could
                // ever observe a second consecutive failure, so the latch could never trip. It is cleared
                // once a mount is confirmed healthy instead.
                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
                this.scheduleContextRefresh(captureContext);
                this.scheduleMountHealthCheck(MOUNT_HEALTH_CHECK_MS);
            },

            /**
             * Store the resolved transient-token JWT and flag a new card so the place-order UI activates.
             *
             * @param {String} transientTokenJwt
             */
            handleTransientToken: function (transientTokenJwt) {
                if (!transientTokenJwt) {
                    return;
                }

                this.transientToken(transientTokenJwt);

                // Card entry is done: the capture context has served its purpose and the token's own
                // lifetime takes over from here. A pending health check is likewise moot.
                this.clearTimer('_contextTimer');
                this.clearTimer('_healthTimer');
                this.scheduleTokenExpiry(transientTokenJwt);

                // Surface a synthetic "new card" so the existing place-order UI (gated on selectedCard) shows.
                this.storedCards.remove(function (card) {
                    return card.id === NEW_CARD_ID;
                });
                this.storedCards.push({
                    id: NEW_CARD_ID,
                    label: $.mage.__('New Card'),
                    selected: true,
                    new: true,
                    type: '',
                    cc_bin: '',
                    cc_last4: ''
                });
                this.selectedCard(NEW_CARD_ID);
            },

            /**
             * Re-request the capture context if the grand total changed after the drop-in was mounted,
             * whether or not a token was already captured.
             *
             * The capture mandate/3DS amount is baked into the capture context (and into any captured
             * transient token), so a total change from a coupon or shipping update after the customer
             * finished card entry leaves that amount stale. remountDropin() -> resetDropinState() clears
             * the captured token + synthetic card, forcing re-entry against a fresh, correctly-priced
             * context. Guards: lastGrandTotal null => never mounted; nothing mounted => nothing to re-mount.
             */
            handleTotalChange: function () {
                if (this.lastGrandTotal === null
                    || !this.isDropinMounted()) {
                    return;
                }

                if (this.getGrandTotal() !== this.lastGrandTotal) {
                    this.remountDropin();
                }
            },

            /**
             * Clear a timer handle by property name, if armed.
             *
             * @param {String} handle
             */
            clearTimer: function (handle) {
                if (this[handle]) {
                    clearTimeout(this[handle]);
                    this[handle] = null;
                }
            },

            /**
             * Schedule a silent re-mount for when the capture context lapses.
             *
             * @param {String} captureContext
             */
            scheduleContextRefresh: function (captureContext) {
                this.clearTimer('_contextTimer');

                this._contextTimer = setTimeout(
                    this.handleContextExpiry.bind(this),
                    ucClient.getRefreshDelay(captureContext)
                );
            },

            /**
             * The capture context has lapsed. Nothing has been entered that a re-mount would destroy, so
             * swap in a fresh one silently — but only if no token was captured against it, in which case
             * the context is already spent and the token timer owns expiry.
             */
            handleContextExpiry: function () {
                this._contextTimer = null;

                if (this.transientToken()) {
                    return;
                }

                this.remountDropin();
            },

            /**
             * Schedule expiry of a captured transient token.
             *
             * @param {String} transientTokenJwt
             */
            scheduleTokenExpiry: function (transientTokenJwt) {
                this.clearTimer('_tokenTimer');

                this._tokenTimer = setTimeout(
                    this.handleTokenExpiry.bind(this),
                    ucClient.getRefreshDelay(transientTokenJwt)
                );
            },

            /**
             * The captured token has lapsed and the server would now reject it, so the card entry the
             * customer already completed has to go. Unlike a context refresh this destroys real work,
             * so it is always announced rather than silently wiping the form under them.
             */
            handleTokenExpiry: function () {
                this._tokenTimer = null;

                this.remountDropin();

                ucClient.showError(
                    $.mage.__('Your payment session expired. Please re-enter your card details.')
                );
            },

            /**
             * Schedule the mount health check.
             *
             * @param {Number} delay - CYCLE_TIMEOUT_MS when arming at cycle open (the library load can
             *                         stall without ever calling back), MOUNT_HEALTH_CHECK_MS once the
             *                         mount is underway and only the paint is outstanding.
             */
            scheduleMountHealthCheck: function (delay) {
                this.clearTimer('_healthTimer');

                this._healthTimer = setTimeout(this.checkMountHealth.bind(this), delay);
            },

            /**
             * Recover from a mount that attached but never became usable (the 0-height iframe case).
             *
             * This is also the only place a mount is confirmed to have actually worked, so it owns
             * clearing the failure latch. Each dead mount counts against that latch, so a persistently
             * broken mount trips it and stops rather than looping on signed capture-context calls; the
             * customer gets told once instead of being left with a silently empty payment area.
             */
            checkMountHealth: function () {
                this._healthTimer = null;

                // The mount cycle ends here however it went, so release the in-flight guard before any
                // branch below can return — a marker left set would block every future mount attempt.
                this._activeGeneration = null;

                if (this.transientToken()
                    || !$('#' + this.getCode() + '_uc_screen').is(':visible')) {
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
             * Tear down captured state and re-request a fresh capture context + drop-in.
             */
            remountDropin: function () {
                this.resetDropinState();

                // resetDropinState() may have flipped selectedCard from NEW_CARD_ID to null, which
                // fires handleSelectedCardChange -> maybeMountDropin and already kicks off a request.
                // Only mount here if that did not happen (e.g. total-change while no token captured),
                // and let the guarded maybeMountDropin avoid a duplicate request either way.
                this.maybeMountDropin();
            },

            /**
             * Tear down any mounted/in-flight drop-in: cancel the expiry timers, abort the in-flight
             * capture-context request, drop captured token + synthetic card, and empty the containers.
             *
             * Called when switching to a stored card and as the first step of a re-mount, so a fresh
             * mount never races a stale request or double-injects into the same containers (I1).
             */
            resetDropinState: function () {
                this.clearTimer('_contextTimer');
                this.clearTimer('_tokenTimer');
                this.clearTimer('_healthTimer');

                // Invalidate any cycle still in flight: bumping the generation makes its callbacks
                // no-ops, so a load or mount started before this teardown cannot land afterwards.
                this._mountGeneration++;
                this._activeGeneration = null;

                if (this._captureXhr) {
                    this._captureXhr.abort();
                    this._captureXhr = null;
                }

                this.transientToken(null);
                this.storedCards.remove(function (card) {
                    return card.id === NEW_CARD_ID;
                });
                if (this.selectedCard() === NEW_CARD_ID) {
                    this.selectedCard(null);
                }

                this.lastGrandTotal = null;

                $('#' + this.getCode() + '_uc_selection').empty();
                $('#' + this.getCode() + '_uc_screen').empty();
            },

            /**
             * KO UI component teardown. Clear the expiry timers, abort any in-flight request, and dispose
             * the quote subscriptions so a re-rendered checkout region does not leave detached handlers
             * or a timer firing against a detached DOM (C2).
             */
            dispose: function () {
                this.clearTimer('_contextTimer');
                this.clearTimer('_tokenTimer');
                this.clearTimer('_healthTimer');

                // Aborting the xhr only stops a cycle still on its ajax leg; one already past it would
                // otherwise mount into the detached DOM this component is being torn down from.
                this._mountGeneration++;
                this._activeGeneration = null;

                if (this._captureXhr) {
                    this._captureXhr.abort();
                    this._captureXhr = null;
                }

                if (this._subscriptions) {
                    this._subscriptions.forEach(function (subscription) {
                        subscription.dispose();
                    });
                    this._subscriptions = [];
                }

                this._super();
            },

            handleAjaxError: function (jqXHR, status, error) {
                // jQuery abort()s surface here with status 'abort'; that is an intentional teardown
                // (stored-card switch / re-mount), not a failure, so swallow it silently.
                if (status === 'abort') {
                    return;
                }

                this._mountFailures++;

                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
                this._captureXhr = null;

                // The cycle died here; release the in-flight guard so a retry is possible.
                this._activeGeneration = null;

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
            },

            /**
             * Build the POST payload for the capture-context endpoint (billing + guest email + form key).
             *
             * @return {Object}
             */
            getCaptureContextParams: function () {
                var billingAddress = _.pick(
                    quote.billingAddress(),
                    [
                        'firstname',
                        'lastname',
                        'company',
                        'street',
                        'city',
                        'regionCode',
                        'regionId',
                        'region',
                        'postcode',
                        'countryId',
                        'telephone'
                    ]
                );

                if (quote.guestEmail !== undefined && quote.guestEmail !== null) {
                    billingAddress.email = quote.guestEmail;
                }

                return {
                    'billing': billingAddress,
                    'source': 'checkout',
                    'guest_email': quote.guestEmail !== undefined ? quote.guestEmail : null,
                    'form_key': this.getFormKey()
                };
            },

            /**
             * Current quote grand total, used to detect amount changes that require a fresh capture context.
             *
             * @return {Number|null}
             */
            getGrandTotal: function () {
                var totals = quote.totals();

                return totals && totals.grand_total !== undefined ? totals.grand_total : null;
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transient_token': this.transientToken(),
                        'card_id': this.selectedCard() === NEW_CARD_ID ? null : this.selectedCard(),
                        'cc_cid': this.creditCardVerificationNumber(),
                        'save': this.save()
                    }
                };
            },

            /**
             * Whether to collect a security code ourselves.
             *
             * Only for a real vaulted card, matching what the setting actually offers ("force customers to
             * enter CCV when using a stored card"). The drop-in collects the code as part of card entry, so
             * asking again for a freshly tokenized card is a pointless second prompt.
             *
             * @return {Boolean}
             */
            hasVerification: function () {
                var selected = this.selectedCard();

                return this.requireCcv() && !!selected && selected !== NEW_CARD_ID;
            },

            getFormKey: function () {
                return $('input[name="form_key"]').val();
            }
        });
    }
);
