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
        var config=window.checkoutConfig.payment.paradoxlabs_cybersource;
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_CyberSource/payment/secure-acceptance',
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : [],
                logoImage: config ? config.logoImage : false,
                billingAddressLine: null,
                responseJWT: null,
                iframeInitialized: false
            },
            initVars: function() {
                this.canSaveCard     = config ? config.canSaveCard : false;
                this.forceSaveCard   = config ? config.forceSaveCard : false;
                this.defaultSaveCard = config ? config.defaultSaveCard : false;
                this.requireCcv      = config ? config.requireCcv : false;
            },
            initObservable: function () {
                this.initVars();
                this._super()
                    .observe([
                        'billingAddressLine',
                        'payerAuthSessionId',
                        'responseJWT'
                    ]);

                quote.billingAddress.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                quote.paymentMethod.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                this.billingAddressLine.subscribe(this.initSecureAcceptanceForm.bind(this));
                this.selectedCard.subscribe(this.checkReinitSecureAcceptanceForm.bind(this));
                this.selectedCard.subscribe(this.doBinLookup.bind(this));

                this.showIframe = ko.computed(function() {
                    return (this.selectedCard() === null || this.selectedCard() === undefined)
                           && quote.billingAddress() !== null;
                }, this);

                this.showSaveOption = ko.computed(function() {
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

                this.storedCards = ko.observableArray(config.storedCards);

                this.useVault = ko.computed(function() {
                    return this.storedCards().length > 0;
                }, this);

                this.loadFingerprint();
                this.loadPayerAuth();

                return this;
            },
            loadFingerprint: function() {
                if (config.fingerprintUrl !== undefined
                    && config.fingerprintUrl !== null
                    && config.fingerprintUrl.length > 1) {
                    // Bypassing requireJS because this is easy enough and bypasses core .min-ifying.
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = config.fingerprintUrl;
                    document.head.appendChild(script);
                }
            },
            syncSecureAcceptBillingAddress: function() {
                // Don't progess until the iframe has rendered, we're the active payment method, we have a billing addr.
                if ($('#' + this.getCode() + '_iframe').length === 0
                    || quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || this.selectedCard()
                    || quote.billingAddress() === null) {
                    return;
                }

                this.billingAddressLine(this.getAddressLine(quote.billingAddress()));
            },
            checkReinitSecureAcceptanceForm: function() {
                if (this.iframeInitialized === false
                    && (this.selectedCard() === null || this.selectedCard() === undefined)
                    && this.storedCards().length > 0) {
                    // The initialized flag is to debounce and ensure we don't reinit unless absolutely necessary.
                    this.iframeInitialized = true;
                    this.initSecureAcceptanceForm();
                }
            },
            initSecureAcceptanceForm: function() {
                this.bindCommunicator();

                // Clear and spinner the CC form while we load new params
                $('#' + this.getCode() + '_iframe').prop('src', 'about:blank')
                                                   .trigger('processStart');

                return $.post({
                    url: config.paramUrl,
                    dataType: 'json',
                    data: this.getFormParams(),
                    global: false,
                    success: this.loadSecureAcceptanceForm.bind(this),
                    error: this.handleAjaxError.bind(this)
                });
            },
            loadSecureAcceptanceForm: function(data) {
                var form = document.createElement('form');
                form.target = this.getCode() + '_iframe';
                form.method = 'post';
                form.action = data.iframeAction;

                for (var key in data.iframeParams) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data.iframeParams[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();

                $('#' + this.getCode() + '_iframe').trigger('processStop');
            },
            handleAjaxError: function(jqXHR, status, error) {
                $('#' + this.getCode() + '_iframe').trigger('processStop');

                var message = $.mage.__('A server error occurred. Please try again.');

                try {
                    var responseJson = JSON.parse(jqXHR.responseText);
                    if (responseJson.message !== undefined) {
                        message = responseJson.message;
                    }
                } catch (error) {}

                try {
                    alert({
                        title: $.mage.__('Error'),
                        content: message
                    });
                } catch (error) {
                    // Fall back to standard alert if jq widget hasn't initialized yet
                    window.alert(message);
                }
            },
            bindCommunicator: function() {
                window.jQuery('#' + this.getCode() + '-communicator')
                    .off('change')
                    .on('change', this.handleCommunication.bind(this));
            },
            handleCommunication: function(event) {
                var value = event.target.value;

                if (value !== undefined && value !== null && value !== '') {
                    var message = JSON.parse(value);

                    if (message.success && message.card !== undefined) {
                        this.storedCards.push(message.card);
                        this.selectedCard(message.card.id);
                        this.iframeInitialized = false;
                    } else if (message.error.length > 0) {
                        if (message.error.indexOf('(101)') === -1 && message.error.indexOf('(102)') === -1) {
                            this.initSecureAcceptanceForm();
                        }

                        alert({
                            title: $.mage.__('Error'),
                            content: message.error
                        });
                    }
                }
            },
            getAddressLine: function(address) {
                if (address === null) {
                    return null;
                }

                if (typeof address.street === 'string') {
                    address.street.split("\n");
                }

                return address.firstname + ' '
                       + address.lastname + ', '
                       + address.street.join(' ') + ', '
                       + address.city + ', '
                       + address.region + ' '
                       + address.postcode + ', '
                       + address.countryId + ' '
                       + address.telephone;
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'card_id': this.selectedCard(),
                        'cc_cid': this.creditCardVerificationNumber(),
                        'payerauth_session_id': this.payerAuthSessionId(),
                        'response_jwt': this.responseJWT(),
                        'save': this.save()
                    }
                }
            },
            getFormParams: function() {
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
                    'card_id': this.selectedCard(),
                    'payerauth_session_id': this.payerAuthSessionId(),
                    'form_key': this.getFormKey()
                }
            },
            hasVerification: function () {
                return this.requireCcv();
            },
            loadPayerAuth: function() {
                if (config.cardinalScript.length > 0 && config.cardinalJWT.length > 0) {
                    // Bypassing requireJS because this is easy enough and bypasses core .min-ifying.
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = config.cardinalScript;
                    script.addEventListener('load', this.initPayerAuth.bind(this));
                    document.head.appendChild(script);
                }
            },
            initPayerAuth: function() {
                Cardinal.configure({
                    payment: {
                        displayLoading: true
                    }
                });

                Cardinal.on('payments.validated', this.handlePayerAuthCompletion.bind(this));
                Cardinal.on('payments.setupComplete', this.handlePayerAuthInit.bind(this));
                Cardinal.setup('init', {jwt: config.cardinalJWT});

                this.doBinLookup(this.selectedCard());
            },
            handlePayerAuthInit: function(responseData) {
                if (responseData && responseData.sessionId !== undefined) {
                    this.payerAuthSessionId(responseData.sessionId);
                }
            },
            handlePayerAuthCompletion: function(responseData, responseJWT) {
                if (responseData.ErrorNumber > 0) {
                    this.responseJWT(null);

                    // If Payer Auth CCA failed, throw the error message and let the user deal with it.
                    alert({
                        title: $.mage.__('Error'),
                        content: $.mage.__(responseData.ErrorDescription) + ' (' + responseData.ErrorNumber + ')'
                    });
                } else {
                    // If Payer Auth CCA succeeded, store the JWT and retry the order.
                    this.responseJWT(responseJWT);
                    this.placeOrder();
                }
            },
            doBinLookup: function(selectedCardId) {
                if (typeof Cardinal === 'object' && selectedCardId !== null && selectedCardId !== undefined) {
                    var cards = this.storedCards();
                    for (var key in cards) {
                        if (cards[key].id === selectedCardId) {
                            Cardinal.trigger('bin.process', cards[key].cc_bin);
                        }
                    }
                }
            },
            getPlaceOrderDeferredObject: function() {
                // Run Cardinal Cruise BIN lookup while the order processes
                this.doBinLookup(this.selectedCard());

                return this._super();
            },
            handleFailedOrder: function(response) {
                this.responseJWT(null);

                var payerAuthMessage = $.mage.__(
                    'The entered card is enrolled in Payer Authentication. Please authenticate before continuing.'
                );
                var error = JSON.parse(response.responseText);
                if (error
                    && typeof error.message !== 'undefined'
                    && typeof Cardinal === 'object'
                    && error.message.indexOf(payerAuthMessage) >= 0) {
                    this.startPayerAuthentication();

                    return;
                }

                return this._super();
            },
            startPayerAuthentication: function() {
                $.post({
                    url: config.cardinalAuthUrl,
                    dataType: 'json',
                    data: this.getFormParams(),
                    global: false,
                    success: this.runPayerAuth.bind(this),
                    error: this.handleAjaxError.bind(this)
                });
            },
            runPayerAuth: function(response) {
                Cardinal.continue(
                    'cca',
                    response.authPayload,
                    response.orderPayload,
                    response.JWT
                );
            },
            getFormKey: function() {
                return $('input[name="form_key"]').val();
            }
        });
    }
);
