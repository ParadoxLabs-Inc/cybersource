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

    // Fallback lifetime for a JWT that carries no usable exp claim. Server-side TTL is ~15 minutes.
    var FALLBACK_TTL_MS = 14 * 60 * 1000;
    // Act this far ahead of a JWT's own exp, so the refresh/warning lands before the server rejects it.
    var EXPIRY_MARGIN_MS = 60 * 1000;
    // Floor on any expiry-driven delay; see getRefreshDelay() for why zero is unsafe.
    var MIN_EXPIRY_DELAY_MS = 30 * 1000;
    // CyberSource numeric card-type codes -> Magento type code + display brand. Must agree with the
    // server-side map in Model/Source/CardType, which remains authoritative for the stored cc_type.
    var CARD_TYPES = {
        '001': {type: 'VI', label: 'Visa'},
        '002': {type: 'MC', label: 'Mastercard'},
        '003': {type: 'AE', label: 'American Express'},
        '004': {type: 'DI', label: 'Discover'},
        '005': {type: 'DN', label: 'Diners Club'},
        '007': {type: 'JCB', label: 'JCB'},
        '042': {type: 'MI', label: 'Maestro'}
    };
    // UC metadata.paymentType values for wallets -> display labels (proper nouns; not translated),
    // for transient tokens that carry no card fields. PANENTRY is deliberately absent.
    var WALLET_LABELS = {
        'APPLEPAY': 'Apple Pay',
        'GOOGLEPAY': 'Google Pay',
        'CLICKTOPAY': 'Click to Pay',
        'PAZE': 'Paze'
    };

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
         * Normalize a transient-token claim field to its scalar string value, or null.
         *
         * Real gda-token payloads wrap scalars as {"value": "..."} objects and serialize absent
         * fields as empty arrays; tolerate wrapped objects, bare scalars, and both absent shapes.
         *
         * @param {*} field
         * @return {String|null}
         */
        getTokenFieldValue: function (field) {
            if (field !== null && typeof field === 'object') {
                field = field.value;
            }

            if (typeof field === 'string' && field.length > 0) {
                return field;
            }

            if (typeof field === 'number') {
                return String(field);
            }

            return null;
        },

        /**
         * Extract display card metadata from a transient-token JWT.
         *
         * The token payload carries the masked number, bin, expiration, and numeric type under
         * content.paymentInformation.card (UC Transient Token Format); wallet tokens may omit some
         * or all card fields but identify themselves via metadata.paymentType. Never throws — any
         * malformed input yields null, and absent fields yield null members.
         *
         * @param {String} jwt
         * @return {Object|null} {type, label, last4, bin, expMonth, expYear, paymentType} or null
         */
        getCardMetadata: function (jwt) {
            try {
                var body = this.decodeJwtBody(jwt);
                var card = body.content
                    && body.content.paymentInformation
                    && body.content.paymentInformation.card
                    || {};
                var number = card.number !== null && typeof card.number === 'object' ? card.number : {};
                var masked = this.getTokenFieldValue(number.maskedValue);
                var last4Match = masked !== null ? masked.match(/(\d{1,4})$/) : null;
                var typeInfo = CARD_TYPES[this.getTokenFieldValue(card.type)] || null;

                return {
                    type: typeInfo ? typeInfo.type : null,
                    label: typeInfo ? typeInfo.label : null,
                    last4: last4Match !== null ? last4Match[1] : null,
                    bin: this.getTokenFieldValue(number.bin),
                    expMonth: this.getTokenFieldValue(card.expirationMonth),
                    expYear: this.getTokenFieldValue(card.expirationYear),
                    paymentType: body.metadata ? this.getTokenFieldValue(body.metadata.paymentType) : null
                };
            } catch (error) {
                return null;
            }
        },

        /**
         * Display label for a UC wallet paymentType, or null for card entry/unknown types.
         *
         * @param {String|null} paymentType
         * @return {String|null}
         */
        getWalletLabel: function (paymentType) {
            return WALLET_LABELS[paymentType] || null;
        },

        /**
         * Expiry of a JWT (capture context or transient token) as an epoch-milliseconds timestamp.
         *
         * Both JWTs carry their own lifetime; reading it beats assuming a fixed TTL, which drifts out of
         * step whenever CyberSource changes the server-side window. Returns null when the token cannot be
         * decoded or carries no numeric exp claim, leaving the caller to fall back.
         *
         * @param {String} jwt
         * @return {Number|null}
         */
        getJwtExpiry: function (jwt) {
            if (typeof jwt !== 'string' || jwt.length === 0) {
                return null;
            }

            try {
                var body = this.decodeJwtBody(jwt);

                return typeof body.exp === 'number' ? body.exp * 1000 : null;
            } catch (error) {
                return null;
            }
        },

        /**
         * How long to wait before acting on a JWT's expiry, from now.
         *
         * Shared by all three UC entry points, which each have to swap a lapsing capture context or
         * discard a lapsing transient token. Floored at MIN_EXPIRY_DELAY_MS rather than zero: expiry is
         * computed against the browser clock, so a skewed client can read a freshly issued JWT as already
         * expired — and a zero delay there means fire -> re-mount -> fetch a context that also looks
         * expired -> fire again, an unbounded loop of signed API calls that a mount-failure latch cannot
         * catch (each mount genuinely succeeds). The floor bounds that to a slow retry.
         *
         * @param {String} jwt
         * @return {Number} milliseconds
         */
        getRefreshDelay: function (jwt) {
            var expiry = this.getJwtExpiry(jwt);

            if (expiry === null) {
                return FALLBACK_TTL_MS;
            }

            return Math.max(MIN_EXPIRY_DELAY_MS, expiry - Date.now() - EXPIRY_MARGIN_MS);
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
         * Whether a mount container currently holds healthy (painted) UC content.
         *
         * Emptiness alone cannot tell a healthy mount from a dead one: the UC iframe can attach at
         * 0x0 (FIT_WINDOW never applied), leaving no visible payment UI while a children-only check
         * reports "mounted". A visible container at zero height is therefore unhealthy. The
         * visibility qualifier matters — a hidden-but-healthy mount legitimately measures zero.
         *
         * @param {jQuery} $container
         * @return {Boolean}
         */
        isContainerHealthy: function ($container) {
            if ($container.length === 0 || $container.children().length === 0) {
                return false;
            }

            return !$container.is(':visible') || $container.height() > 0;
        },

        /**
         * Remove UC helper iframes left attached to document.body by superseded Accept() instances.
         *
         * Each Accept() mount appends side iframes (buttonlist plumbing, overlays) directly to the
         * body, outside the caller's containers, so emptying those containers on teardown never
         * reaches them and every re-mount would otherwise leave another set behind. Callers invoke
         * this while tearing down — the live instance is being discarded too, so removing all UC
         * frames (testup/up.cybersource.com, /uc/ asset path) is safe.
         */
        reapOrphanedFrames: function () {
            $('body > iframe[src*=".cybersource.com/uc/"]').remove();
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
