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

/**
 * Shared Unified Checkout client helpers, consumed by the three UC entry points (the KO renderer,
 * the legacy jQuery widget, and the customer/admin add-card widget). Factors the genuinely identical
 * plumbing — JWT decoding, SRI script injection (de-duped by src), the Accept mount chain, and the
 * alert error surface — while each entry point keeps its own distinct ajax/error-routing behavior.
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert) {
    'use strict';

    return {
        /**
         * Base64url-decode the JWT payload (middle segment) and parse as JSON.
         *
         * @param {String} jwt
         * @return {Object}
         */
        decodeJwtBody: function (jwt) {
            var payload = jwt.split('.')[1];
            payload = payload.replace(/-/g, '+').replace(/_/g, '/');
            while (payload.length % 4) {
                payload += '=';
            }

            return JSON.parse(window.atob(payload));
        },

        /**
         * Decode the capture-context JWT and inject the UC client library (with SRI when the capture
         * context provides an integrity hash), then invoke onReady once it is available.
         *
         * De-duped by script src: a long session re-mounts every ~14 minutes and every capture context
         * points at the same CDN library, so we never append a second identical tag. If a tag with that
         * src already exists and window.Accept is a function the library is loaded, so onReady fires
         * immediately; if the tag exists but is still loading we attach to its load/error events rather
         * than injecting a duplicate.
         *
         * @param {String} captureContext
         * @param {Function} onReady
         * @param {Function} onError - called with a message string on decode failure or script error
         */
        loadClientLibrary: function (captureContext, onReady, onError) {
            var ctx;

            try {
                ctx = this.decodeJwtBody(captureContext).ctx[0].data;
            } catch (error) {
                onError('Invalid capture context');

                return;
            }

            var existing = document.querySelector('script[src="' + ctx.clientLibrary + '"]');

            if (existing) {
                // Already injected. If the library finished loading proceed immediately; otherwise
                // attach to the in-flight tag rather than injecting a duplicate.
                if (typeof window.Accept === 'function') {
                    onReady();

                    return;
                }

                existing.addEventListener('load', onReady);
                existing.addEventListener('error', function () {
                    onError('Unable to load payment library');
                });

                return;
            }

            // Bypassing requireJS because UC.js is an external asset served from CyberSource's CDN.
            var script = document.createElement('script');
            script.src = ctx.clientLibrary;
            if (ctx.clientLibraryIntegrity) {
                script.integrity = ctx.clientLibraryIntegrity;
                script.crossOrigin = 'anonymous';
            }
            script.addEventListener('load', onReady);
            script.addEventListener('error', function () {
                onError('Unable to load payment library');
            });
            document.getElementsByTagName('head')[0].appendChild(script);
        },

        /**
         * Mount the UC drop-in via the Accept global into the embedded containers and return the
         * promise resolving to the captured transient-token JWT (callers chain their own handlers).
         *
         * @param {String} captureContext
         * @param {String} selectionSelector - CSS selector for the paymentSelection container
         * @param {String} screenSelector - CSS selector for the paymentScreen container
         * @return {Promise}
         */
        mountUnifiedPayments: function (captureContext, selectionSelector, screenSelector) {
            return Accept(captureContext)
                .then(function (accept) {
                    // false = embedded layout (sidebar rejects the paymentScreen container)
                    return accept.unifiedPayments(false);
                })
                .then(function (unifiedPayments) {
                    return unifiedPayments.show({
                        containers: {
                            paymentSelection: selectionSelector,
                            paymentScreen: screenSelector
                        }
                    });
                });
        },

        /**
         * Surface an error via the jq alert widget, falling back to window.alert if it has not
         * initialized yet.
         *
         * @param {String} message
         */
        showError: function (message) {
            try {
                alert({
                    title: $.mage.__('Error'),
                    content: message
                });
            } catch (e) {
                // Fall back to standard alert if jq widget hasn't initialized yet
                window.alert(message);
            }
        }
    };
});
