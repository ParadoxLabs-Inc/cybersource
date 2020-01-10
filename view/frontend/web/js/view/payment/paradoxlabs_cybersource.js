/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'paradoxlabs_cybersource',
                component: 'ParadoxLabs_CyberSource/js/view/payment/method-renderer/paradoxlabs_cybersource'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
