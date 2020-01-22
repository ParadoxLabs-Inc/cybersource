define(
    [
        'ko',
        'jquery',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/model/quote'
    ],
    function (ko, $, Component, alert, quote) {
        'use strict';
        var config=window.checkoutConfig.payment.paradoxlabs_cybersource;
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_CyberSource/payment/secure-acceptance',
                isFormShown: true,
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : {},
                logoImage: config ? config.logoImage : false,
                cardToken: null,
                billingAddressLine: null
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'billingAddressLine',
                        'cardToken' // TODO: Change this
                    ]);

                quote.billingAddress.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                quote.paymentMethod.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                this.billingAddressLine.subscribe(this.initSecureAcceptanceForm.bind(this));

                this.storedCards = ko.observableArray(config.storedCards);

                this.useVault = ko.computed(function() {
                    return this.getStoredCards().length > 0;
                }, this);

                this.isCardEntered = ko.computed(function () {
                    return this.cardToken() !== undefined
                           && this.cardToken() !== '';
                }, this);

                return this;
            },
            syncSecureAcceptBillingAddress: function() {
                // Has the template rendered? Don't process until it has.
                if ($('#' + this.getCode() + '_iframe').length === 0
                    || quote.billingAddress() === null
                    || quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()) {
                    return;
                }

                this.billingAddressLine(this.getAddressLine(quote.billingAddress()));
            },
            initSecureAcceptanceForm: function() {
                this.bindCommunicator();

                // Clear and spinner the CC form while we load new params
                $('#' + this.getCode() + '_iframe').prop('src', 'about:blank')
                                                   .trigger('processStart');

                return $.post({
                    url: config.paramUrl,
                    dataType: 'json',
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
                // TODO: Make this better
                alert($.mage.__('Payment request failed: ' + error));
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

                    console.log('Got iframe message!', message);

                    if (message.success) {
                        this.cardToken('abc123');

                        this.storedCards.push({
                            "id": "abc123",
                            "label": "VI XXXX-1111",
                            "selected": true,
                            "type": "VI"
                        });
                    } else {
                        this.cardToken(null);
                    }
                }
            },
            getStoredCards: function() {
                return this.storedCards;
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
                       + address.countryId;
            }
        });
    }
);
