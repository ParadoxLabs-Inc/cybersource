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
    'ParadoxLabs_CyberSource/js/unified-checkout-client',
    'mage/translate',
    'mage/validation'
], function ($, ucClient) {
    'use strict';

    // Transient token TTL is ~15 minutes; re-request the capture context shortly before it lapses.
    var TOKEN_TTL_MS = 14 * 60 * 1000;

    // ---------------------------------------------------------------------------------------------
    // Iter-3 additional_data contract (shared with the KO renderer):
    //   New card  => transient_token = <UC transient-token JWT>, card_id empty/absent.
    // This is a dedicated add-card form (no stored-card select); handleTransientToken() strips the
    // card_id field before submit so only the transient_token represents the new card.
    // ---------------------------------------------------------------------------------------------

    $.widget('mage.cybersourcePaymentInfoForm', {
        options: {
            captureContextUrl: null,
            tokenSelector: '[name="payment[transient_token]"]',
            fieldPrefix: '#'
        },

        _create: function () {
            this.element.find('#submit-address').on('click', this.showPayment.bind(this));
            this.element.find('#edit-address').on('click', this.showAddress.bind(this));
            this.dropinMounted = false;
            this._captureXhr = null;
            this._ttlTimer = null;
        },

        _destroy: function () {
            if (this._ttlTimer) {
                clearTimeout(this._ttlTimer);
                this._ttlTimer = null;
            }

            if (this._captureXhr) {
                this._captureXhr.abort();
                this._captureXhr = null;
            }
        },

        showAddress: function () {
            this.element.find('.address').show();
            this.element.find('.payment').hide();

            // Returning to edit the address invalidates the mounted drop-in/token; tear it down so a
            // fresh capture context (with the updated billing address) is requested on the next mount.
            this.teardownDropin();
        },

        /**
         * Tear down a mounted/in-flight drop-in: cancel the TTL timer, abort the request, clear the
         * captured token and empty the containers so the next mount starts clean.
         */
        teardownDropin: function () {
            if (this._ttlTimer) {
                clearTimeout(this._ttlTimer);
                this._ttlTimer = null;
            }

            if (this._captureXhr) {
                this._captureXhr.abort();
                this._captureXhr = null;
            }

            this.element.find(this.options.tokenSelector).val('');
            this.element.find('.unified-checkout-selection').empty();
            this.element.find('.unified-checkout-screen').empty();
            this.dropinMounted = false;
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
            // Guard against a duplicate-mount race: an in-flight request or an already populated
            // screen container means a mount is pending/done; do not issue a second request (I1).
            if (this.dropinMounted === true
                || this._captureXhr !== null
                || this.element.find('.unified-checkout-screen').children().length > 0) {
                return;
            }

            this.dropinMounted = true;

            this.element.find('.unified-checkout-screen').trigger('processStart');

            this._captureXhr = $.post({
                url: this.options.captureContextUrl,
                dataType: 'json',
                data: this.element.serialize(),
                global: false,
                success: this.loadClientLibrary.bind(this),
                error: this.handleAjaxError.bind(this)
            });

            this._captureXhr.always(function () {
                this._captureXhr = null;
            }.bind(this));

            return this._captureXhr;
        },

        /**
         * Re-request a fresh capture context + drop-in before the transient-token TTL lapses, so a
         * long customer/admin session does not submit a stale token (M5).
         */
        remountDropin: function () {
            this.teardownDropin();
            this.mountDropin();
        },

        /**
         * Decode the capture-context JWT, inject UC.js (with SRI), then mount the drop-in.
         */
        loadClientLibrary: function (data, status, jqXHR) {
            if (!data || !data.captureContext) {
                return this.handleAjaxError(jqXHR, status, data);
            }

            this.captureContext = data.captureContext;

            ucClient.loadClientLibrary(
                data.captureContext,
                this.mountUnifiedCheckout.bind(this),
                function (message) {
                    this.handleAjaxError(null, 'error', message);
                }.bind(this)
            );
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

            ucClient.mountUnifiedPayments(this.captureContext, '#' + selection, '#' + screen)
                .then(this.handleTransientToken.bind(this))
                .catch(function (error) {
                    this.handleAjaxError(null, 'error', error && error.message ? error.message : null);
                }.bind(this));

            this.element.find('.unified-checkout-screen').trigger('processStop');

            if (this._ttlTimer) {
                clearTimeout(this._ttlTimer);
            }

            this._ttlTimer = setTimeout(this.remountDropin.bind(this), TOKEN_TTL_MS);
        },

        /**
         * Stash the transient token in the hidden form input, then submit the form.
         *
         * The form posts to the TokenBase paymentinfo save controller, which exchanges the transient
         * token for a vault card (via Card::beforeSave) and performs its own redirect + success/error
         * message. We always submit here; the dedicated add-card form carries no stored-card select, so
         * the card_id field is stripped before submit to leave only the transient_token representing the
         * new card.
         */
        handleTransientToken: function (transientTokenJwt) {
            if (!transientTokenJwt) {
                return;
            }

            this.element.find(this.options.tokenSelector).val(transientTokenJwt);

            this.element.find('input[name=card_id]').attr('name', '');
            this.element.submit();
        },

        handleAjaxError: function (jqXHR, status, error) {
            // Aborts are intentional teardown (address edit / re-mount), not failures.
            if (status === 'abort') {
                return;
            }

            this.element.find('.unified-checkout-screen').trigger('processStop');
            this.dropinMounted = false;
            this._captureXhr = null;

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
                // responseText was not JSON; keep the default/passed message.
            }

            ucClient.showError(message);
        }
    });

    return $.mage.cybersourcePaymentInfoForm;
});
