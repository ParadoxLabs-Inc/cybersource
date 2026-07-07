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
        // Transient token TTL is ~15 minutes; re-request the capture context shortly before it lapses.
        var TOKEN_TTL_MS = 14 * 60 * 1000;
        // Placeholder card id used to flag a freshly tokenized (not-yet-vaulted) card to the place-order UI.
        var NEW_CARD_ID = 'unified_checkout_new';

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
                // stale request instead of double-mounting. TTL timer handle for the same reason.
                this._captureXhr = null;
                this._ttlTimer = null;

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
             * The guard keys on actual container emptiness rather than a sticky boolean, so a
             * stored -> new card transition reliably re-mounts (a sticky latch would dead-end).
             */
            maybeMountDropin: function () {
                var screen = $('#' + this.getCode() + '_uc_screen');

                if (quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || this.selectedCard()
                    || quote.billingAddress() === null
                    || screen.length === 0
                    || this._captureXhr !== null
                    || screen.children().length > 0) {
                    return;
                }

                this.requestCaptureContext();
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

                if (selected === NEW_CARD_ID) {
                    return;
                }

                if (selected) {
                    // A real stored card is now selected; abandon any pending/mounted drop-in.
                    this.resetDropinState();

                    return;
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

                this._captureXhr = $.post({
                    url: config.captureContextUrl,
                    dataType: 'json',
                    data: this.getCaptureContextParams(),
                    global: false,
                    success: this.loadClientLibrary.bind(this),
                    error: this.handleAjaxError.bind(this)
                });

                // Clear the in-flight handle once settled so a later total change can mount again.
                this._captureXhr.always(function () {
                    this._captureXhr = null;
                }.bind(this));

                return this._captureXhr;
            },

            /**
             * Decode the capture-context JWT, inject the UC client library (with SRI), then mount the drop-in.
             *
             * @param {Object} data
             */
            loadClientLibrary: function (data) {
                if (!data || !data.captureContext) {
                    this.handleAjaxError(null, 'error', 'Missing capture context');

                    return;
                }

                this.captureContext = data.captureContext;

                ucClient.loadClientLibrary(
                    data.captureContext,
                    this.mountUnifiedCheckout.bind(this),
                    function (message) {
                        this.handleAjaxError(null, 'error', message);
                    }.bind(this)
                );
            },

            /**
             * Mount the UC drop-in via the Accept global into the embedded containers.
             */
            mountUnifiedCheckout: function () {
                if (typeof Accept !== 'function') {
                    this.handleAjaxError(null, 'error', 'Payment library unavailable');

                    return;
                }

                ucClient.mountUnifiedPayments(
                    this.captureContext,
                    '#' + this.getCode() + '_uc_selection',
                    '#' + this.getCode() + '_uc_screen'
                )
                    .then(this.handleTransientToken.bind(this))
                    .catch(function (error) {
                        this.handleAjaxError(null, 'error', error && error.message ? error.message : null);
                    }.bind(this));

                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
                this.scheduleTokenRefresh();
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
             * context. Guards: lastGrandTotal null => never mounted; empty containers => nothing mounted.
             */
            handleTotalChange: function () {
                if (this.lastGrandTotal === null
                    || $('#' + this.getCode() + '_uc_screen').children().length === 0) {
                    return;
                }

                if (this.getGrandTotal() !== this.lastGrandTotal) {
                    this.remountDropin();
                }
            },

            /**
             * Schedule a re-mount before the transient token TTL lapses.
             */
            scheduleTokenRefresh: function () {
                if (this._ttlTimer) {
                    clearTimeout(this._ttlTimer);
                }

                this._ttlTimer = setTimeout(this.remountDropin.bind(this), TOKEN_TTL_MS);
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
             * Tear down any mounted/in-flight drop-in: cancel the TTL timer, abort the in-flight
             * capture-context request, drop captured token + synthetic card, and empty the containers.
             *
             * Called when switching to a stored card and as the first step of a re-mount, so a fresh
             * mount never races a stale request or double-injects into the same containers (I1).
             */
            resetDropinState: function () {
                if (this._ttlTimer) {
                    clearTimeout(this._ttlTimer);
                    this._ttlTimer = null;
                }

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
             * KO UI component teardown. Clear the TTL timer, abort any in-flight request, and dispose
             * the quote subscriptions so a re-rendered checkout region does not leave detached handlers
             * or a timer firing against a detached DOM (C2).
             */
            dispose: function () {
                if (this._ttlTimer) {
                    clearTimeout(this._ttlTimer);
                    this._ttlTimer = null;
                }

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

                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
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

            hasVerification: function () {
                return this.requireCcv();
            },

            getFormKey: function () {
                return $('input[name="form_key"]').val();
            }
        });
    }
);
