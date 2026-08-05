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
 * Shared Payer Authentication (3D Secure 2) client. Owns the four browser-side primitives the
 * three-step server contract needs — browser profiling, device data collection, the issuer
 * challenge, and the REST transport — and nothing else: no retries, no messaging, no state. The
 * caller (the Luma renderer today, a Hyvä component later) sequences them and owns all UX.
 *
 * The "REST transport" section is the only Luma-coupled code in the file; the DDC and challenge
 * machinery is plain DOM, so a Hyvä port replaces post() and supplies its own styles.
 *
 * The challenge modal carries classes and no inline styling — see the pl-payerauth rules in
 * view/base/web/css/source/_module.less. It is therefore unstyled until that stylesheet applies,
 * which is safe on checkout: a shopper who has reached payment has already loaded the theme CSS.
 *
 * postMessage discipline mirrors the wrapper page (Controller/Payerauth/Challenge): every message
 * both ways carries source: 'pl-cybersource-payerauth', and every message from the wrapper is
 * checked for BOTH same-origin and identity of the sending window. The DDC collector is third-party
 * and gets the weaker window-identity check only — see runDdc.
 */
define([
    'mage/storage',
    'mage/url',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'mage/translate'
], function (storage, coreUrl, urlBuilder, quote, customer, $t) {
    'use strict';

    // Message source tag. Must match Controller/Payerauth/Challenge and Controller/Payerauth/Callback.
    var TAG = 'pl-cybersource-payerauth';
    // Ceiling on device data collection. The collector is best-effort by design (its result reaches
    // CyberSource out of band, keyed by the reference id), so a silent collector must not block the
    // sale — the timeout is the normal exit, not an error path.
    var DDC_TIMEOUT_MS = 10000;
    // Ceiling on an issuer challenge. EMV 3DS gives the cardholder ten minutes; past that the ACS
    // itself has abandoned the transaction, so there is nothing left to wait for.
    var CHALLENGE_TIMEOUT_MS = 600000;
    // Frame/element id counter, so overlapping attempts (a retry begun before the previous frame is
    // reaped) can never collide on a window name — a form target resolves by name, and a duplicate
    // would post the CReq or the JWT into the wrong frame.
    var sequence = 0;

    /**
     * Next unique DOM id / window name for this page load.
     *
     * @param {String} prefix
     * @return {String}
     */
    function nextId(prefix) {
        sequence++;

        return prefix + '-' + sequence + '-' + Date.now();
    }

    /**
     * Read a result field that may arrive in either casing.
     *
     * Magento's REST renderer emits data-interface getters snake_cased (getAccessToken =>
     * access_token; Magento\Framework\Reflection\FieldNamer), while SOAP/GraphQL and any future
     * hand-rolled response would carry camelCase. Reading both keeps the client honest against the
     * documented contract without depending on which serializer answered.
     *
     * @param {Object} result
     * @param {String} camelKey
     * @param {String} snakeKey
     * @return {*}
     */
    function field(result, camelKey, snakeKey) {
        if (!result || typeof result !== 'object') {
            return undefined;
        }

        return result[camelKey] !== undefined ? result[camelKey] : result[snakeKey];
    }

    // -------------------------------------------------------------------------------------------
    // REST transport (the one Luma-coupled seam)
    // -------------------------------------------------------------------------------------------

    /**
     * Build the service URL for a payer-auth action, resolving the cart the way core checkout does:
     * a logged-in customer addresses carts/mine (the cart comes from the session token), a guest
     * addresses guest-carts by the masked id that checkoutConfig already carries.
     *
     * Mirrors Magento_Checkout/js/model/resource-url-manager (getUrl/getCheckoutMethod); that module
     * is not reused directly because its url maps are its own private literals.
     *
     * @param {String} action - setup|authenticate|finalize
     * @return {String}
     */
    function getServiceUrl(action) {
        if (customer.isLoggedIn()) {
            return urlBuilder.createUrl('/carts/mine/paradoxlabs-cybersource/payer-auth/' + action, {});
        }

        return urlBuilder.createUrl(
            '/guest-carts/:cartId/paradoxlabs-cybersource/payer-auth/' + action,
            {cartId: quote.getQuoteId()}
        );
    }

    /**
     * Turn a failed jqXHR into an Error carrying the server's own message where there is one.
     *
     * Magento webapi faults serialize as {message, parameters}; the message is a translation
     * template with %fieldName / %1 placeholders, filled here so the caller can display it directly.
     * The raw response is never logged or attached — it can echo request data.
     *
     * @param {Object} xhr
     * @return {Error}
     */
    function toError(xhr) {
        var body = xhr && xhr.responseJSON;
        var message = body && typeof body.message === 'string' ? body.message : '';
        var parameters = body && body.parameters;
        var key;

        if (message === '') {
            return new Error($t('Payment authentication is unavailable. Please try again.'));
        }

        if (parameters && typeof parameters === 'object') {
            for (key in parameters) {
                if (parameters.hasOwnProperty(key)) {
                    message = message.split('%' + key).join(String(parameters[key]));
                }
            }
        }

        return new Error(message);
    }

    /**
     * POST a payload to one of the three payer-auth endpoints.
     *
     * Shared by setup/authenticate/finalize; the only difference between them is the action segment
     * and the body. global:false keeps the request off Magento's global ajax error surface — payer
     * auth failures are the renderer's to narrate, in the payment form, not as a page-level error.
     *
     * @param {String} action - setup|authenticate|finalize
     * @param {Object} payload
     * @return {Promise<Object>} resolves with the parsed response; rejects with an Error on failure
     */
    function post(action, payload) {
        return new Promise(function (resolve, reject) {
            storage.post(getServiceUrl(action), JSON.stringify(payload), false)
                .done(function (response) {
                    resolve(response);
                })
                .fail(function (xhr) {
                    reject(toError(xhr));
                });
        });
    }

    // -------------------------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------------------------

    return {
        /**
         * Collect the browser profile the ACS uses for risk scoring and challenge sizing.
         *
         * Exactly the seven fields the server's browser-info contract accepts; the remaining 3DS2
         * browser fields (user agent, accept header, IP) are derived server-side from the request
         * and are deliberately not sent from here. Booleans stay real booleans — the server maps
         * them to the strings CyberSource wants. javaEnabled reads false on every modern browser
         * and is still sent explicitly, because omitting it is not the same signal as false.
         *
         * @return {Object} {language, javaEnabled, javaScriptEnabled, colorDepth, screenHeight,
         *                   screenWidth, timeDifference}
         */
        collectBrowserInfo: function () {
            var screen = window.screen || {};

            return {
                language: window.navigator.language || '',
                javaEnabled: (typeof window.navigator.javaEnabled === 'function'
                    && window.navigator.javaEnabled()) || false,
                javaScriptEnabled: true,
                colorDepth: screen.colorDepth,
                screenHeight: screen.height,
                screenWidth: screen.width,
                timeDifference: new Date().getTimezoneOffset()
            };
        },

        /**
         * Step 1: start an attempt and get the device-data-collection handles.
         *
         * Exactly one of transientToken (newly entered card) or cardHash (stored card) belongs in
         * the payload; the server rejects both and neither. A skipped result means payer auth does
         * not apply to this attempt and the caller should proceed straight to placing the order.
         *
         * @param {Object} payload - {transientToken: String} | {cardHash: String}
         * @return {Promise<Object>} {skipped, accessToken|access_token,
         *                            deviceDataCollectionUrl|device_data_collection_url}
         */
        setup: function (payload) {
            return post('setup', payload || {});
        },

        /**
         * Step 2: run the enrollment check for the attempt setup() started.
         *
         * @param {Object} browserInfo - as returned by collectBrowserInfo()
         * @param {String} [returnUrl] - absolute HTTPS URL; omitted entirely when not given, so the
         *                               server's own challenge-return route applies
         * @return {Promise<Object>} {status, acsUrl|acs_url, pareq}
         */
        authenticate: function (browserInfo, returnUrl) {
            var payload = {browserInfo: browserInfo};

            if (typeof returnUrl === 'string' && returnUrl !== '') {
                payload.returnUrl = returnUrl;
            }

            return post('authenticate', payload);
        },

        /**
         * Step 3: finalize a challenged authentication, after the issuer step-up has returned.
         *
         * Takes nothing: the attempt is identified server-side by the quote. The outcome is read
         * from CyberSource, never from anything the challenge posted back.
         *
         * @return {Promise<Object>} {status, acsUrl|acs_url, pareq}
         */
        finalize: function () {
            return post('finalize', {});
        },

        /**
         * Run 3DS device data collection: form-POST the access token as JWT into a hidden iframe
         * pointed at the collector, and wait briefly for its completion message.
         *
         * Best-effort by contract: the collector's payload reaches CyberSource out of band, so this
         * NEVER rejects and never hangs — waiting only improves the odds the profile lands before
         * the enrollment check runs.
         *
         * The iframe is 0x0 and positioned off-screen rather than display:none: display:none frames
         * are not laid out, and collectors that measure or paint can stall in one.
         *
         * The completion message comes from a third-party origin that is not portably knowable, so
         * it gets the window-identity check instead of an origin check. It carries no data we act
         * on — its arrival, or the timeout, is the entire signal.
         *
         * @param {String} accessToken - the setup result's access token (a JWT)
         * @param {String} deviceDataCollectionUrl - the setup result's collector URL
         * @return {Promise<Object>} always resolves: {status: 'completed'|'timeout'|'skipped'}
         */
        runDdc: function (accessToken, deviceDataCollectionUrl) {
            if (typeof accessToken !== 'string' || accessToken === ''
                || typeof deviceDataCollectionUrl !== 'string' || deviceDataCollectionUrl === '') {
                return Promise.resolve({status: 'skipped'});
            }

            return new Promise(function (resolve) {
                var name = nextId('pl-pa-ddc');
                var frame = document.createElement('iframe');
                var form = document.createElement('form');
                var input = document.createElement('input');
                var timer = null;
                var settled = false;

                /**
                 * Resolve once, and take the whole apparatus down with it.
                 *
                 * @param {String} status
                 * @return {void}
                 */
                function settle(status) {
                    if (settled) {
                        return;
                    }

                    settled = true;
                    window.removeEventListener('message', onMessage, false);

                    if (timer !== null) {
                        window.clearTimeout(timer);
                    }

                    if (form.parentNode) {
                        form.parentNode.removeChild(form);
                    }

                    if (frame.parentNode) {
                        frame.parentNode.removeChild(frame);
                    }

                    resolve({status: status});
                }

                /**
                 * Accept a completion message from our own collector frame only.
                 *
                 * @param {MessageEvent} event
                 * @return {void}
                 */
                function onMessage(event) {
                    if (!frame.contentWindow || event.source !== frame.contentWindow) {
                        return;
                    }

                    settle('completed');
                }

                frame.setAttribute('id', name);
                frame.setAttribute('name', name);
                frame.setAttribute('title', 'Device data collection');
                frame.setAttribute('width', '0');
                frame.setAttribute('height', '0');
                frame.setAttribute('frameborder', '0');
                frame.setAttribute('aria-hidden', 'true');
                // Stays inline, unlike the challenge modal's styling: this is concealment, not
                // theming, and a stylesheet that has not applied yet would flash the collector
                // iframe onto the checkout. Nothing here is a merchant-facing surface.
                frame.style.position = 'absolute';
                frame.style.left = '-9999px';
                frame.style.top = '0';
                frame.style.width = '0';
                frame.style.height = '0';
                frame.style.border = '0';

                form.setAttribute('method', 'POST');
                form.setAttribute('action', deviceDataCollectionUrl);
                form.setAttribute('target', name);
                form.style.display = 'none';

                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'JWT');
                input.value = accessToken;
                form.appendChild(input);

                window.addEventListener('message', onMessage, false);
                document.body.appendChild(frame);
                document.body.appendChild(form);

                timer = window.setTimeout(function () {
                    settle('timeout');
                }, DDC_TIMEOUT_MS);

                form.submit();
            });
        },

        /**
         * Run the issuer challenge in a modal, and resolve when it comes back, is cancelled, or
         * times out.
         *
         * The modal frames OUR OWN same-origin wrapper page (pdl_cybs/payerauth/challenge), never
         * the ACS directly: the wrapper receives acsUrl and pareq by postMessage — keeping both out
         * of any URL, history entry, or access log — POSTs the CReq into its own child frame, and
         * relays the return event back up. Nothing here talks to the issuer.
         *
         * Both messages from the wrapper are checked for same-origin AND for coming from the frame
         * we created. A hostile same-origin frame could at most forge 'return' early, which costs a
         * finalize call that re-reads the real outcome from CyberSource.
         *
         * Never rejects: every ending is a status the caller can act on. Cancellation is by the
         * close control or Escape only — a backdrop click does not close a payment authentication.
         *
         * Returns a handle rather than a bare promise so the caller can abort a challenge it no
         * longer wants (e.g. the drop-in remounts mid-challenge). cancel() settles the promise as
         * 'cancelled' and reaps the modal; it is a no-op once the challenge has settled.
         *
         * @param {String} acsUrl - issuer ACS endpoint from the authenticate/finalize result
         * @param {String} pareq - the challenge request payload (CReq) for that ACS
         * @return {{promise: Promise<Object>, cancel: Function}} promise resolves
         *         {status: 'return'|'cancelled'|'timeout'|'error'}
         */
        runChallenge: function (acsUrl, pareq) {
            if (typeof acsUrl !== 'string' || acsUrl === '' || typeof pareq !== 'string' || pareq === '') {
                return {promise: Promise.resolve({status: 'error'}), cancel: function () {}};
            }

            // Hoisted out of the executor so the returned cancel() can settle the challenge from
            // outside. The executor runs synchronously, so this is assigned before runChallenge returns.
            var settleRef = null;
            var promise = new Promise(function (resolve) {
                var origin = window.location.origin;
                var name = nextId('pl-pa-challenge');
                var previousFocus = document.activeElement;
                var overlay = document.createElement('div');
                var dialog = document.createElement('div');
                var header = document.createElement('div');
                var heading = document.createElement('h2');
                var close = document.createElement('button');
                var frame = document.createElement('iframe');
                var headingId = name + '-title';
                var timer = null;
                var settled = false;
                var started = false;

                /**
                 * Resolve once and reap the modal, the frame, and both listeners.
                 *
                 * @param {String} status
                 * @return {void}
                 */
                function settle(status) {
                    if (settled) {
                        return;
                    }

                    settled = true;
                    window.removeEventListener('message', onMessage, false);
                    document.removeEventListener('keydown', onKeyDown, false);

                    if (timer !== null) {
                        window.clearTimeout(timer);
                    }

                    if (overlay.parentNode) {
                        overlay.parentNode.removeChild(overlay);
                    }

                    if (previousFocus && typeof previousFocus.focus === 'function') {
                        previousFocus.focus();
                    }

                    resolve({status: status});
                }

                settleRef = settle;

                /**
                 * Handle the wrapper's two messages: hand it the challenge when it reports ready,
                 * and settle when it relays the return.
                 *
                 * @param {MessageEvent} event
                 * @return {void}
                 */
                function onMessage(event) {
                    var data = event.data;

                    if (!data || data.source !== TAG || event.origin !== origin) {
                        return;
                    }

                    if (!frame.contentWindow || event.source !== frame.contentWindow) {
                        return;
                    }

                    if (data.event === 'ready' && !started) {
                        started = true;
                        frame.contentWindow.postMessage(
                            {source: TAG, event: 'challenge', acsUrl: acsUrl, pareq: pareq},
                            origin
                        );
                    } else if (data.event === 'return') {
                        settle('return');
                    }
                }

                /**
                 * Escape cancels, matching the close control.
                 *
                 * @param {KeyboardEvent} event
                 * @return {void}
                 */
                function onKeyDown(event) {
                    if (event.key === 'Escape' || event.keyCode === 27) {
                        settle('cancelled');
                    }
                }

                overlay.setAttribute('class', 'pl-payerauth-overlay');

                dialog.setAttribute('class', 'pl-payerauth-modal');
                dialog.setAttribute('role', 'dialog');
                dialog.setAttribute('aria-modal', 'true');
                dialog.setAttribute('aria-labelledby', headingId);

                header.setAttribute('class', 'pl-payerauth-header');

                heading.setAttribute('id', headingId);
                heading.setAttribute('class', 'pl-payerauth-title');
                heading.appendChild(document.createTextNode($t('Verify Your Payment')));

                close.setAttribute('type', 'button');
                close.setAttribute('class', 'action-close pl-payerauth-close');
                close.setAttribute('aria-label', $t('Cancel payment verification'));
                close.appendChild(document.createTextNode('×'));
                close.addEventListener('click', function () {
                    settle('cancelled');
                }, false);

                frame.setAttribute('id', name);
                frame.setAttribute('name', name);
                frame.setAttribute('class', 'pl-payerauth-frame');
                frame.setAttribute('title', $t('Payment Authentication'));
                frame.setAttribute('frameborder', '0');

                header.appendChild(heading);
                header.appendChild(close);
                dialog.appendChild(header);
                dialog.appendChild(frame);
                overlay.appendChild(dialog);

                window.addEventListener('message', onMessage, false);
                document.addEventListener('keydown', onKeyDown, false);
                document.body.appendChild(overlay);

                // Source set after attaching, so the wrapper's 'ready' message can never beat the
                // listener. The nonce is only a cache-buster; the wrapper ignores all parameters.
                frame.setAttribute(
                    'src',
                    coreUrl.build('pdl_cybs/payerauth/challenge') + '?_=' + Date.now()
                );

                if (typeof close.focus === 'function') {
                    close.focus();
                }

                timer = window.setTimeout(function () {
                    settle('timeout');
                }, CHALLENGE_TIMEOUT_MS);
            });

            return {
                promise: promise,
                cancel: function () {
                    if (settleRef !== null) {
                        settleRef('cancelled');
                    }
                }
            };
        },

        /**
         * Read a field from a setup/authenticate/finalize result regardless of the serializer's
         * casing. Exposed so the caller does not have to duplicate the snake_case tolerance.
         *
         * @param {Object} result
         * @param {String} camelKey - e.g. 'accessToken'
         * @param {String} snakeKey - e.g. 'access_token'
         * @return {*}
         */
        getResultField: function (result, camelKey, snakeKey) {
            return field(result, camelKey, snakeKey);
        }
    };
});
