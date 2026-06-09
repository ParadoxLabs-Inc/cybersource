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
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/model/quote',
        'mage/translate'
    ],
    function (ko, $, _, Component, alert, quote) {
        'use strict';
        var config = window.checkoutConfig.payment.paradoxlabs_cybersource;
        // Transient token TTL is ~15 minutes; re-request the capture context shortly before it lapses.
        var TOKEN_TTL_MS = 14 * 60 * 1000;
        // Placeholder card id used to flag a freshly tokenized (not-yet-vaulted) card to the place-order UI.
        var NEW_CARD_ID = 'unified_checkout_new';
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_CyberSource/payment/unified-checkout',
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : [],
                logoImage: config ? config.logoImage : false,
                transientToken: null,
                dropinMounted: false,
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

                quote.billingAddress.subscribe(this.maybeMountDropin.bind(this));
                quote.paymentMethod.subscribe(this.maybeMountDropin.bind(this));
                this.selectedCard.subscribe(this.maybeMountDropin.bind(this));

                // Re-request the capture context whenever the grand total changes, so the amount in the
                // capture mandate stays in sync with the order being placed.
                quote.totals.subscribe(this.handleTotalChange.bind(this));

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

                return this;
            },

            /**
             * Mount the Unified Checkout drop-in when we are the active method, have a billing address,
             * are adding a new card, and have not already mounted.
             */
            maybeMountDropin: function () {
                if (this.dropinMounted === true
                    || quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || this.selectedCard()
                    || quote.billingAddress() === null
                    || $('#' + this.getCode() + '_uc_screen').length === 0) {
                    return;
                }

                this.dropinMounted = true;
                this.requestCaptureContext();
            },

            /**
             * Request a capture-context JWT from the frontend endpoint, then load the UC client library.
             *
             * @return {jqXHR}
             */
            requestCaptureContext: function () {
                $('#' + this.getCode() + '_uc_screen').trigger('processStart');
                this.lastGrandTotal = this.getGrandTotal();

                return $.post({
                    url: config.captureContextUrl,
                    dataType: 'json',
                    data: this.getCaptureContextParams(),
                    global: false,
                    success: this.loadClientLibrary.bind(this),
                    error: this.handleAjaxError.bind(this)
                });
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

                var ctx;
                try {
                    ctx = this.decodeJwtBody(data.captureContext).ctx[0].data;
                } catch (error) {
                    this.handleAjaxError(null, 'error', 'Invalid capture context');

                    return;
                }

                // Bypassing requireJS because UC.js is an external asset served from CyberSource's CDN.
                var script = document.createElement('script');
                script.src = ctx.clientLibrary;
                if (ctx.clientLibraryIntegrity) {
                    script.integrity = ctx.clientLibraryIntegrity;
                    script.crossOrigin = 'anonymous';
                }
                script.addEventListener('load', this.mountUnifiedCheckout.bind(this));
                script.addEventListener('error', function () {
                    this.handleAjaxError(null, 'error', 'Unable to load payment library');
                }.bind(this));
                document.getElementsByTagName('head')[0].appendChild(script);
            },

            /**
             * Mount the UC drop-in via the Accept global into the embedded containers.
             */
            mountUnifiedCheckout: function () {
                if (typeof Accept !== 'function') {
                    this.handleAjaxError(null, 'error', 'Payment library unavailable');

                    return;
                }

                Accept(this.captureContext)
                    .then(function (accept) {
                        return accept.unifiedPayments();
                    })
                    .then(function (unifiedPayments) {
                        return unifiedPayments.show({
                            containers: {
                                paymentSelection: '#' + this.getCode() + '_uc_selection',
                                paymentScreen: '#' + this.getCode() + '_uc_screen'
                            }
                        });
                    }.bind(this))
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
             * Re-request the capture context if the grand total changed while the drop-in was mounted
             * but before a token was captured.
             */
            handleTotalChange: function () {
                if (this.dropinMounted !== true
                    || this.transientToken()
                    || this.lastGrandTotal === null) {
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
                this.transientToken(null);
                this.storedCards.remove(function (card) {
                    return card.id === NEW_CARD_ID;
                });
                if (this.selectedCard() === NEW_CARD_ID) {
                    this.selectedCard(null);
                }

                $('#' + this.getCode() + '_uc_selection').empty();
                $('#' + this.getCode() + '_uc_screen').empty();

                this.requestCaptureContext();
            },

            /**
             * Base64url-decode the JWT payload (middle segment) and parse as JSON.
             *
             * @param {String} jwt
             * @return {Object}
             */
            decodeJwtBody: function (jwt) {
                var payload = jwt.split('.')[1];
                payload = payload.replace(/-/g, '+').replace(/_/g, '/');
                while (payload.length % 4) {
                    payload += '=';
                }

                return JSON.parse(window.atob(payload));
            },

            handleAjaxError: function (jqXHR, status, error) {
                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
                this.dropinMounted = false;

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
                }

                try {
                    alert({
                        title: $.mage.__('Error'),
                        content: message
                    });
                } catch (e) {
                    // Fall back to standard alert if jq widget hasn't initialized yet
                    window.alert(message);
                }
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
