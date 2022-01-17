/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @category    ParadoxLabs
 * @package     CyberSource
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

/*jshint jquery:true*/
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function($, alert) {
    'use strict';

    $.widget('mage.cybersourcePaymentInfoForm', {
        options: {
            target: null,
            paramUrl: null,
            successUrl: null,
            fieldPrefix: '#'
        },

        _create: function() {
            this.element.find('#submit-address').on('click', this.showPayment.bind(this));
            this.element.find('#edit-address').on('click', this.showAddress.bind(this));
        },

        showAddress: function() {
            this.element.find('.address').show();
            this.element.find('.payment').hide();
        },

        showPayment: function() {
            if (this.element.valid() === false) {
                return;
            }

            this.renderAddress();

            this.element.find('.address').hide();
            this.element.find('.payment').show();

            this.fixScroll();

            this.initSecureAcceptanceForm();
        },

        renderAddress: function() {
            var address = $(this.options.fieldPrefix + 'firstname').val() + ' ';
            address += $(this.options.fieldPrefix + 'lastname').val() + '<br />';
            address += $(this.options.fieldPrefix + 'company').val()
                       ? $(this.options.fieldPrefix + 'company').val() + '<br />'
                       : '';
            address += $(this.options.fieldPrefix + 'street').val() + '<br />';
            address += $(this.options.fieldPrefix + 'street_2').val()
                       ? $(this.options.fieldPrefix + 'street_2').val() + '<br />'
                       : '';
            address += $(this.options.fieldPrefix + 'city').val() + ', ';
            address += $(this.options.fieldPrefix + 'region-id option:selected').text()
                       ? $(this.options.fieldPrefix + 'region-id option:selected').text() + ' '
                       : $(this.options.fieldPrefix + 'region').val() + ' ';
            address += $(this.options.fieldPrefix + 'zip').val() + '<br />';
            address += $(this.options.fieldPrefix + 'country option:selected').text() + '<br />';
            address += $(this.options.fieldPrefix + 'telephone').val()
                       ? $(this.options.fieldPrefix + 'telephone').val()
                       : '';

            this.element.find('address').html(address);
        },

        fixScroll: function() {
            var topPosition = $('fieldset.payment:first').position().top;

            if (topPosition < window.scrollY) {
                window.scrollTo(0, topPosition);
            }
        },

        initSecureAcceptanceForm: function() {
            this.bindCommunicator();

            // Clear and spinner the CC form while we load new params
            this.element.find('iframe').prop('src', 'about:blank')
                .trigger('processStart');

            return $.post({
                url: this.options.paramUrl,
                dataType: 'json',
                data: this.element.serialize(),
                global: false,
                success: this.loadSecureAcceptanceForm.bind(this),
                error: this.handleAjaxError.bind(this)
            });
        },

        loadSecureAcceptanceForm: function(data) {
            var form = document.createElement('form');
            form.target = this.options.target;
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

            this.element.find('iframe').trigger('processStop');
        },

        handleAjaxError: function(jqXHR, status, error) {
            this.element.find('iframe').trigger('processStop');

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
            window.jQuery('#paradoxlabs_cybersource-communicator')
                .off('change')
                .on('change', this.handleCommunication.bind(this));
        },

        handleCommunication: function(event) {
            var value = event.target.value;

            if (value !== undefined && value !== null && value !== '') {
                var message = JSON.parse(value);

                if (message.success && message.card !== undefined) {
                    if (this.options.successUrl !== null) {
                        // If we have a success URL, redirect there (frontend behavior)
                        window.location.href = this.options.successUrl;
                        this.element.trigger('processStart');
                    } else {
                        // Trigger form submission to reload the section (admin behavior)
                        this.element.find('input[name=card_id]').attr('name', '');
                        this.element.submit();
                    }
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
        }
    });

    return $.mage.cybersourcePaymentInfoForm;
});
