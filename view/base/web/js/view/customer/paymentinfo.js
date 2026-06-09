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
    'mage/translate',
    'mage/validation'
], function ($, alert) {
    'use strict';

    $.widget('mage.cybersourcePaymentInfoForm', {
        options: {
            captureContextUrl: null,
            successUrl: null,
            tokenSelector: '[name="transient_token"]',
            fieldPrefix: '#'
        },

        _create: function () {
            this.element.find('#submit-address').on('click', this.showPayment.bind(this));
            this.element.find('#edit-address').on('click', this.showAddress.bind(this));
            this.dropinMounted = false;
        },

        showAddress: function () {
            this.element.find('.address').show();
            this.element.find('.payment').hide();
        },

        showPayment: function () {
            this.element.validation();
            if (this.element.validation('isValid') !== true) {
                return;
            }

            this.renderAddress();

            this.element.find('.address').hide();
            this.element.find('.payment').show();

            this.fixScroll();

            this.mountDropin();
        },

        renderAddress: function () {
            var address = $(this.options.fieldPrefix + 'firstname').val() + ' ';
            address += $(this.options.fieldPrefix + 'lastname').val() + '<br>';
            address += $(this.options.fieldPrefix + 'company').val()
                       ? $(this.options.fieldPrefix + 'company').val() + '<br>'
                       : '';
            address += $(this.options.fieldPrefix + 'street').val() + '<br>';
            address += $(this.options.fieldPrefix + 'street_2').val()
                       ? $(this.options.fieldPrefix + 'street_2').val() + '<br>'
                       : '';
            address += $(this.options.fieldPrefix + 'city').val() + ', ';
            address += $(this.options.fieldPrefix + 'region-id option:selected').text()
                       ? $(this.options.fieldPrefix + 'region-id option:selected').text() + ' '
                       : $(this.options.fieldPrefix + 'region').val() + ' ';
            address += $(this.options.fieldPrefix + 'zip').val() + '<br>';
            address += $(this.options.fieldPrefix + 'country option:selected').text() + '<br>';
            address += $(this.options.fieldPrefix + 'telephone').val()
                       ? $(this.options.fieldPrefix + 'telephone').val()
                       : '';

            this.element.find('address').html(address);
        },

        fixScroll: function () {
            var topPosition = $('fieldset.payment:first').position().top;

            if (topPosition < window.scrollY) {
                window.scrollTo(0, topPosition);
            }
        },

        /**
         * Request a capture context and load the UC client library (once).
         */
        mountDropin: function () {
            if (this.dropinMounted === true) {
                return;
            }

            this.dropinMounted = true;

            this.element.find('.unified-checkout-screen').trigger('processStart');

            return $.post({
                url: this.options.captureContextUrl,
                dataType: 'json',
                data: this.element.serialize(),
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
         * Stash the transient token in the hidden form input, then submit/redirect.
         */
        handleTransientToken: function (transientTokenJwt) {
            if (!transientTokenJwt) {
                return;
            }

            this.element.find(this.options.tokenSelector).val(transientTokenJwt);

            if (this.options.successUrl !== null) {
                window.location.href = this.options.successUrl;
                this.element.trigger('processStart');
            } else {
                this.element.find('input[name=card_id]').attr('name', '');
                this.element.submit();
            }
        },

        handleAjaxError: function (jqXHR, status, error) {
            this.element.find('.unified-checkout-screen').trigger('processStop');
            this.dropinMounted = false;

            var message = $.mage.__('A server error occurred. Please try again.');

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

    return $.mage.cybersourcePaymentInfoForm;
});
