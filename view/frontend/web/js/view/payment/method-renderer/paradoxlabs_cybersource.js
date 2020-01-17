define(
    [
        'ko',
        'jquery',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc'
    ],
    function (ko, $, Component) {
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
                cardToken: ''
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'cardToken' // TODO: Change this
                    ]);

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
            initSecureAcceptanceForm: function() {
                var self = this;
                window.jQuery('#' + this.getCode() + '-communicator').on('change', this.handleCommunication.bind(this));

                var form = $('#' + this.getCode() + '_trigger');
                form.attr('action', config.iframeAction);

                for (var key in config.iframeParams) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = config.iframeParams[key];
                    form.append(input);
                }

                form[0].submit();
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
        });
    }
);
