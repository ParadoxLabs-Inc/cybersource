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
], function ($, alert) {
    'use strict';

    // Placeholder card id used to flag a freshly tokenized (not-yet-vaulted) card to the form.
    var NEW_CARD_ID = 'unified_checkout_new';

    $.widget('mage.cybersourceLegacyForm', {
        options: {
            captureContextUrl: null,
            tokenSelector: '[name="payment[transient_token]"]',
            cardSelector: '[name="payment[card_id]"]'
        },

        _create: function () {
            this.element.on('change', this.options.cardSelector, this.handleCardSelectChange.bind(this));
            this.dropinMounted = false;

            this.handleCardSelectChange();
        },

        handleCardSelectChange: function () {
            if (this.element.find(this.options.cardSelector).val() !== '') {
                this.element.find('div.cvv').show();
                this.element.find('div.save').toggle(
                    !!this.element.find(this.options.cardSelector + ' option:selected').data('new')
                );

                return;
            }

            // Hide additional fields when the drop-in is visible
            this.element.find('div.cvv').hide();
            this.element.find('div.save').hide();

            // Mount the UC drop-in if 'add new card' is selected
            this.mountDropin();
        },

        /**
         * Request a capture context and load the UC client library (once).
         */
        mountDropin: function () {
            if (this.dropinMounted === true) {
                return;
            }

            this.dropinMounted = true;

            var screen = this.element.find('.unified-checkout-screen');
            screen.trigger('processStart');

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
                url: this.options.captureContextUrl,
                dataType: 'json',
                data: payload,
                global: false,
                success: this.loadClientLibrary.bind(this),
                error: this.handleAjaxError.bind(this)
            });
        },

        /**
         * Decode the capture-context JWT, inject UC.js (with SRI), then mount the drop-in.
         */
        loadClientLibrary: function (data, status, jqXHR) {
            if (!data || !data.captureContext) {
                return this.handleAjaxError(jqXHR, status, data);
            }

            this.captureContext = data.captureContext;

            var ctx;
            try {
                ctx = this.decodeJwtBody(data.captureContext).ctx[0].data;
            } catch (error) {
                return this.handleAjaxError(null, 'error', 'Invalid capture context');
            }

            var script = document.createElement('script');
            script.src = ctx.clientLibrary;
            if (ctx.clientLibraryIntegrity) {
                script.integrity = ctx.clientLibraryIntegrity;
                script.crossOrigin = 'anonymous';
            }
            script.addEventListener('load', this.mountUnifiedCheckout.bind(this));
            script.addEventListener('error', function () {
                this.handleAjaxError(null, 'error', 'Unable to load payment library');
            }.bind(this));
            document.head.appendChild(script);
        },

        /**
         * Mount the UC drop-in via the Accept global into the embedded containers.
         */
        mountUnifiedCheckout: function () {
            if (typeof Accept !== 'function') {
                return this.handleAjaxError(null, 'error', 'Payment library unavailable');
            }

            var selection = this.element.find('.unified-checkout-selection').attr('id');
            var screen = this.element.find('.unified-checkout-screen').attr('id');

            Accept(this.captureContext)
                .then(function (accept) {
                    return accept.unifiedPayments();
                })
                .then(function (unifiedPayments) {
                    return unifiedPayments.show({
                        containers: {
                            paymentSelection: '#' + selection,
                            paymentScreen: '#' + screen
                        }
                    });
                })
                .then(this.handleTransientToken.bind(this))
                .catch(function (error) {
                    this.handleAjaxError(null, 'error', error && error.message ? error.message : null);
                }.bind(this));

            this.element.find('.unified-checkout-screen').trigger('processStop');
        },

        /**
         * Stash the transient token in the hidden form input and add/select a card so the form submits.
         */
        handleTransientToken: function (transientTokenJwt) {
            if (!transientTokenJwt) {
                return;
            }

            this.element.find(this.options.tokenSelector).val(transientTokenJwt);

            this.addAndSelectCard({
                id: NEW_CARD_ID,
                label: $.mage.__('New Card'),
                new: true,
                type: '',
                cc_bin: '',
                cc_last4: ''
            });
        },

        handleAjaxError: function (jqXHR, status, error) {
            var screen = this.element.find('.unified-checkout-screen');
            var message = $.mage.__('A server error occurred. Please try again.');

            screen.trigger('processStop');
            this.dropinMounted = false;

            if (typeof error === 'string' && error.length > 0) {
                message = error;
            }

            try {
                if (jqXHR && jqXHR.responseText) {
                    var responseJson = JSON.parse(jqXHR.responseText);
                    if (responseJson.message !== undefined) {
                        message = responseJson.message;
                    }
                }
            } catch (e) {
            }

            if (screen.siblings('.message').length > 0) {
                screen.siblings('.message').text(message).show();
                screen.hide();

                return;
            }

            try {
                alert({
                    title: $.mage.__('Error'),
                    content: message
                });
            } catch (e) {
                // Fall back to standard alert if jq widget hasn't initialized yet
                window.alert(message);
            }
        },

        addAndSelectCard: function (card) {
            var option = $('<option>');
            option.val(card.id)
                .text(card.label)
                .data('new', card.new)
                .data('cc_bin', card.cc_bin)
                .data('cc_last4', card.cc_last4)
                .data('type', card.type);

            this.element.find(this.options.cardSelector).append(option).val(card.id).trigger('change');
        },

        /**
         * Base64url-decode the JWT payload (middle segment) and parse as JSON.
         */
        decodeJwtBody: function (jwt) {
            var payload = jwt.split('.')[1];
            payload = payload.replace(/-/g, '+').replace(/_/g, '/');
            while (payload.length % 4) {
                payload += '=';
            }

            return JSON.parse(window.atob(payload));
        }
    });

    return $.mage.cybersourceLegacyForm;
});
