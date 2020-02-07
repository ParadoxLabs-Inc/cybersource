define(
    [
        'ko',
        'jquery',
        'underscore',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/model/quote'
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
                iframeCompleted: false
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'billingAddressLine'
                    ]);

                // TODO: Wire require-cvv-for-stored-cards back up

                quote.billingAddress.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                quote.paymentMethod.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                this.billingAddressLine.subscribe(this.initSecureAcceptanceForm.bind(this));
                this.selectedCard.subscribe(this.checkReinitSecureAcceptanceForm.bind(this));

                this.showIframe = ko.computed(function() {
                    return (this.selectedCard() === null || this.selectedCard() === undefined)
                           && quote.billingAddress() !== null;
                }, this);

                this.storedCards = ko.observableArray(config.storedCards);

                this.useVault = ko.computed(function() {
                    return this.storedCards().length > 0;
                }, this);

                return this;
            },
            syncSecureAcceptBillingAddress: function() {
                // Don't progess until the iframe has rendered, we're the active payment method, we have a billing addr.
                if ($('#' + this.getCode() + '_iframe').length === 0
                    || quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || !this.showIframe()) {
                    return;
                }

                // TODO: Something isn't right here. Not catching changes when it should.
                this.billingAddressLine(this.getAddressLine(quote.billingAddress()));
            },
            checkReinitSecureAcceptanceForm: function() {
                if (this.iframeCompleted === true
                    && (this.selectedCard() === null || this.selectedCard() === undefined)
                    && this.storedCards().length > 0) {
                    // The completed flag is to debounce and ensure we don't reinit unless absolutely necessary
                    // (on reselect 'add new' after completing the process).
                    this.iframeCompleted = false;
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
                        "billingAddress": billingAddress
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
                // TODO: Make this better
                alert($.mage.__('Payment request failed: ' + error));

                this.initSecureAcceptanceForm();
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

                    // TODO: Remove debug line
                    console.log('Got iframe message!', message);

                    if (message.success && message.card !== undefined) {
                        this.iframeCompleted = true;
                        this.storedCards.push(message.card);
                        this.selectedCard(message.card.id);
                    } else if (message.success === false && message.error.length > 0) {
                        this.iframeCompleted = false;
                        this.initSecureAcceptanceForm();

                        alert(message.error);
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
