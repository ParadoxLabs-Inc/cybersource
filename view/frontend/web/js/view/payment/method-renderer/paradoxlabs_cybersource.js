define(
    [
        'ko',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc'
    ],
    function (ko, Component) {
        'use strict';
        var config=window.checkoutConfig.payment.paradoxlabs_cybersource;
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_TokenBase/payment/cc',
                isFormShown: true,
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : {},
                logoImage: config ? config.logoImage : false
            },
            initVars: function() {
                this.defaultSaveCard = config ? config.defaultSaveCard : false;
            },
            getCode: function () {
                return 'paradoxlabs_cybersource';
            }
        });
    }
);
