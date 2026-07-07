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
    // Transient token TTL is ~15 minutes; re-request the capture context shortly before it lapses.
    var TOKEN_TTL_MS = 14 * 60 * 1000;

    // ---------------------------------------------------------------------------------------------
    // Iter-3 additional_data contract (shared with the KO renderer):
    //   New card  => transient_token = <UC transient-token JWT>, card_id empty/absent.
    //   Stored card => transient_token empty, card_id = <vault hash>.
    // The synthetic NEW_CARD_ID placeholder is a UI affordance only; addAndSelectCard() clears the
    // submitted card_id value so the placeholder never reaches the server as a real card_id.
    // ---------------------------------------------------------------------------------------------

    $.widget('mage.cybersourceLegacyForm', {
        options: {
            captureContextUrl: null,
            fingerprintUrl: null,
            tokenSelector: '[name="payment[transient_token]"]',
            cardSelector: '[name="payment[card_id]"]'
        },

        _create: function () {
            this.element.on('change', this.options.cardSelector, this.handleCardSelectChange.bind(this));
            this.dropinMounted = false;
            this._captureXhr = null;
            this._ttlTimer = null;

            this.initFingerprint();
            this.handleCardSelectChange();
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

        handleCardSelectChange: function () {
            var select = this.element.find(this.options.cardSelector);
            var selectedOption = this.element.find(this.options.cardSelector + ' option:selected');

            // A real stored card is selected (non-empty value that is not the captured-token placeholder).
            if (select.val() !== '' && selectedOption.data('id') !== NEW_CARD_ID) {
                this.element.find('div.cvv').show();
                this.element.find('div.save').hide();
                this.teardownDropin();

                return;
            }

            // The freshly tokenized "new card" placeholder (value cleared, flagged via data-id).
            if (selectedOption.data('id') === NEW_CARD_ID) {
                this.element.find('div.cvv').show();
                this.element.find('div.save').toggle(!!selectedOption.data('new'));

                return;
            }

            // 'Add new card' selected: hide additional fields and (re-)mount the drop-in.
            this.element.find('div.cvv').hide();
            this.element.find('div.save').hide();

            this.mountDropin();
        },

        /**
         * Tear down a mounted/in-flight drop-in when switching to a stored card, so returning to
         * 'Add new card' re-requests a fresh capture context instead of dead-ending on the latch.
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
            this.element.find(this.options.cardSelector + ' option').filter(function () {
                return $(this).data('id') === NEW_CARD_ID;
            }).remove();
            this.element.find('.unified-checkout-selection').empty();
            this.element.find('.unified-checkout-screen').empty();
            this.dropinMounted = false;
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

            this._captureXhr = $.post({
                url: this.options.captureContextUrl,
                dataType: 'json',
                data: payload,
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
         * long admin/customer session does not submit a stale token (M5).
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
                    // false = embedded layout (sidebar rejects the paymentScreen container)
                    return accept.unifiedPayments(false);
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

            if (this._ttlTimer) {
                clearTimeout(this._ttlTimer);
            }

            this._ttlTimer = setTimeout(this.remountDropin.bind(this), TOKEN_TTL_MS);
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
            // Aborts are intentional teardown (stored-card switch / re-mount), not failures.
            if (status === 'abort') {
                return;
            }

            var screen = this.element.find('.unified-checkout-screen');
            var message = $.mage.__('A server error occurred. Please try again.');

            screen.trigger('processStop');
            this.dropinMounted = false;
            this._captureXhr = null;

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
            // The option carries an EMPTY value so the synthetic placeholder is never submitted as a
            // real card_id (Iter-3 contract); the new-card state is tracked via data-id instead. The
            // transient_token already holds the captured JWT, which is what the server consumes.
            var option = $('<option>');
            option.val('')
                .text(card.label)
                .data('id', card.id)
                .data('new', card.new)
                .data('cc_bin', card.cc_bin)
                .data('cc_last4', card.cc_last4)
                .data('type', card.type);

            var select = this.element.find(this.options.cardSelector);
            select.find('option').prop('selected', false);
            select.append(option);
            option.prop('selected', true);
            select.trigger('change');
        },

        /**
         * Inject the CyberSource Decision Manager (online-metrix) device-fingerprint tag.
         *
         * Legacy parity: the SA/SOAP widget loaded this so DM could collect the device signal keyed on the
         * per-quote session id. Same null/length guard as the legacy widget, plus a de-dupe by URL so a
         * re-init does not append a second identical tag.
         */
        initFingerprint: function () {
            if (this.options.fingerprintUrl === null
                || this.options.fingerprintUrl.length <= 1) {
                return;
            }

            if (document.querySelector('script[data-cybs-fingerprint="' + this.options.fingerprintUrl + '"]')) {
                return;
            }

            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = this.options.fingerprintUrl;
            script.setAttribute('data-cybs-fingerprint', this.options.fingerprintUrl);
            document.head.appendChild(script);
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
