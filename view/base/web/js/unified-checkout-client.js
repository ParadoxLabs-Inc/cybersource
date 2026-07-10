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
 * plumbing — JWT decoding, RequireJS-based UC.js loading (UMD; see loadClientLibrary) with SRI, the
 * Accept mount chain, and the alert error surface — while each entry point keeps its own distinct
 * ajax/error-routing behavior.
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
         * Decode the capture-context JWT and load the UC client library (with SRI when the capture
         * context provides an integrity hash), then invoke onReady once it is available.
         *
         * UC.js is a UMD bundle: when an AMD loader is present (define.amd — always true on a Magento
         * page) it registers via an anonymous define() and NEVER sets window.Accept, so a raw script
         * tag dead-ends with a RequireJS "mismatched anonymous define" error and no usable global.
         * Loading through require([url]) matches the anonymous define to the request and hands us the
         * export object ({Accept}) directly. RequireJS also de-dupes repeat loads of the same URL.
         *
         * SRI: RequireJS creates the script node itself, so the integrity/crossorigin attributes are
         * applied via an onNodeCreated hook keyed by URL (each capture context carries a unique
         * clientLibrary URL + hash).
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

            this.installSriHook();
            this._integrities[ctx.clientLibrary] = ctx.clientLibraryIntegrity || null;

            require(
                [ctx.clientLibrary],
                function (ucModule) {
                    // AMD path: exports object. Non-AMD fallback (just in case): window.Accept.
                    this._accept = ucModule && typeof ucModule.Accept === 'function'
                        ? ucModule.Accept
                        : (typeof window.Accept === 'function' ? window.Accept : null);

                    if (this._accept === null) {
                        onError('Unable to load payment library');

                        return;
                    }

                    onReady();
                }.bind(this),
                function (loadError) {
                    // RequireJS caches load failures per module id; undef so a retry can refetch.
                    if (loadError && loadError.requireModules) {
                        loadError.requireModules.forEach(function (moduleId) {
                            window.require.undef(moduleId);
                        });
                    }

                    onError('Unable to load payment library');
                }
            );
        },

        /**
         * Install the RequireJS onNodeCreated hook that applies SRI attributes to UC.js script nodes,
         * chaining any previously configured hook. Idempotent; keyed off the URL->integrity map.
         */
        installSriHook: function () {
            if (this._integrities) {
                return;
            }

            this._integrities = {};

            var self = this;
            var previousHook = window.require.s
                && window.require.s.contexts
                && window.require.s.contexts._
                && window.require.s.contexts._.config.onNodeCreated;

            require.config({
                onNodeCreated: function (node, config, name, url) {
                    if (self._integrities[url]) {
                        node.setAttribute('integrity', self._integrities[url]);
                        node.setAttribute('crossorigin', 'anonymous');
                    }

                    if (typeof previousHook === 'function') {
                        previousHook.apply(this, arguments);
                    }
                }
            });
        },

        /**
         * Whether the UC client library has been loaded and its Accept entry point captured.
         *
         * @return {Boolean}
         */
        isAvailable: function () {
            return typeof this._accept === 'function' || typeof window.Accept === 'function';
        },

        /**
         * Mount the UC drop-in via the captured Accept entry point into the embedded containers and
         * return the promise resolving to the captured transient-token JWT (callers chain their own
         * handlers).
         *
         * @param {String} captureContext
         * @param {String} selectionSelector - CSS selector for the paymentSelection container
         * @param {String} screenSelector - CSS selector for the paymentScreen container
         * @return {Promise}
         */
        mountUnifiedPayments: function (captureContext, selectionSelector, screenSelector) {
            var accept = typeof this._accept === 'function' ? this._accept : window.Accept;

            return accept(captureContext)
                .then(function (acceptInstance) {
                    // false = embedded layout (sidebar rejects the paymentScreen container)
                    return acceptInstance.unifiedPayments(false);
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
