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
                logoImage: config ? config.logoImage : false
            },
            initialize: function () {
                this._super();
            },
            initSecureAcceptanceForm: function() {
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
            }
        });
    }
);
