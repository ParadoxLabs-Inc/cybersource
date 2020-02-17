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
                        'billingAddressLine'
                    ]);

                quote.billingAddress.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                quote.paymentMethod.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                this.billingAddressLine.subscribe(this.initSecureAcceptanceForm.bind(this));
                this.selectedCard.subscribe(this.checkReinitSecureAcceptanceForm.bind(this));

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

                    for (var card of this.storedCards()) {
                        if (card.id === this.selectedCard()) {
                            return card.new;
                        }
                    }

                    return false;
                }, this);

                this.storedCards = ko.observableArray(config.storedCards);

                this.useVault = ko.computed(function() {
                    return this.storedCards().length > 0;
                }, this);

                this.initFingerprint();

                return this;
            },
            initFingerprint: function() {
                if (config.fingerprintUrl !== undefined
                    && config.fingerprintUrl !== null
                    && config.fingerprintUrl.length > 1) {
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

                var billingAddress = _.pick(
                    quote.billingAddress(),
                    [
                        'firstname',
                        'lastname',
                        'company',
                        'street',
                        'city',
                        'regionCode',
                        'region',
                        'postcode',
                        'countryId',
                        'telephone'
                    ]
                );

                return $.post({
                    url: config.paramUrl,
                    dataType: 'json',
                    data: {
                        'billingAddress': billingAddress
                    },
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
                        this.initSecureAcceptanceForm();

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

                return address.firstname + ' '
                       + address.lastname + ', '
                       + address.street.join(' ') + ', '
                       + address.city + ', '
                       + address.region + ' '
                       + address.postcode + ', '
                       + address.countryId + ' '
                       + address.telephone;
            }
        });
    }
);
