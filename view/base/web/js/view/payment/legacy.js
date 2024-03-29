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

/*jshint jquery:true*/
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function($, alert) {
    'use strict';

    $.widget('mage.cybersourceLegacyForm', {
        options: {
            target: null,
            paramUrl: null,
            fingerprintUrl: null,
            cardSelector: '[name="payment[card_id]"]',
            communicatorSelector: null
        },

        _create: function() {
            this.element.on('change', this.options.cardSelector, this.handleCardSelectChange.bind(this));

            this.initFingerprint();
            this.handleCardSelectChange();
        },

        handleCardSelectChange: function() {
            if (this.element.find(this.options.cardSelector).val() !== '') {
                this.element.find('div.cvv').show();
                this.element.find('div.save').toggle(
                    !!this.element.find(this.options.cardSelector + ' option:selected').data('new')
                );

                return;
            }

            // Hide additional fields when iframe is visible
            this.element.find('div.cvv').hide();
            this.element.find('div.save').hide();

            // Re/init iframe if 'add new card' is selected
            this.initSecureAcceptanceForm();
        },

        initSecureAcceptanceForm: function() {
            this.bindCommunicator();

            // Clear and spinner the CC form while we load new params
            this.element.find('iframe').prop('src', 'about:blank')
                .trigger('processStart');

            var payload = {};
            var inputs = this.element.find(':input');
            for (var key = 0; key < inputs.length; key++) {
                if (inputs[key] === undefined
                    || inputs[key] === null
                    || inputs[key].name === undefined
                    || inputs[key].name.length === 0) {
                    continue;
                }

                payload[inputs[key].name] = $(inputs[key]).val();
            }

            return $.post({
                url: this.options.paramUrl,
                dataType: 'json',
                data: payload,
                global: false,
                success: this.loadSecureAcceptanceForm.bind(this),
                error: this.handleAjaxError.bind(this)
            });
        },

        loadSecureAcceptanceForm: function(data, status, jqXHR) {
            if (data.iframeAction === undefined) {
                return this.handleAjaxError(jqXHR, status, data);
            }

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
            var iframe  = this.element.find('iframe');
            var message = $.mage.__('A server error occurred. Please try again.');

            iframe.trigger('processStop');

            try {
                var responseJson = JSON.parse(jqXHR.responseText);
                if (responseJson.message !== undefined) {
                    message = responseJson.message;
                }
            } catch (error) {}

            if (iframe.siblings('.message').length > 0) {
                iframe.siblings('.message').text(message).show();
                iframe.hide();
                return;
            }

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
            window.jQuery(this.options.communicatorSelector)
                .off('change')
                .on('change', this.handleCommunication.bind(this));
        },

        handleCommunication: function(event) {
            var value = event.target.value;

            if (value !== undefined && value !== null && value !== '') {
                var message = JSON.parse(value);

                if (message.success && message.card !== undefined) {
                    this.addAndSelectCard(message.card);
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

        addAndSelectCard: function(card) {
            var option = $('<option>');
            option.val(card.id)
                .text(card.label)
                .data('new', card.new)
                .data('cc_bin', card.cc_bin)
                .data('cc_last4', card.cc_last4)
                .data('type', card.type);

            this.element.find(this.options.cardSelector).append(option).val(card.id).trigger('change');
        },

        initFingerprint: function() {
            if (this.options.fingerprintUrl !== null
                && this.options.fingerprintUrl.length > 1) {
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = this.options.fingerprintUrl;
                document.head.appendChild(script);
            }
        }
    });

    return $.mage.cybersourceLegacyForm;
});
