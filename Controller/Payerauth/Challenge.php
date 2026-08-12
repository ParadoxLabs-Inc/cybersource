<?php declare(strict_types=1);
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
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Controller\Payerauth;

use Magento\Csp\Api\CspAwareActionInterface;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\MessageProtocol;

/**
 * Same-origin wrapper page that hosts the issuer's 3DS challenge.
 *
 * The checkout frames this page and postMessages the challenge parameters to it; the page never takes
 * them from the URL, keeping the challenge handles out of URLs, logs, and referrers. It then starts
 * the challenge in its own child iframe, in whichever of the two documented transports the
 * enrollment reply supplied (accounts provisioned for the Cardinal-hosted step-up return both
 * stepUpUrl+accessToken and the raw pair; others return acsUrl+pareq only):
 *
 * - step-up (preferred when present): form-POST JWT=<accessToken> to the Cardinal step-up URL;
 *   Cardinal's frame runs the CReq/CRes exchange with the ACS itself, then POSTs to our return URL.
 * - raw EMV (CyberSource Direct API shape): form-POST creq=<pareq> straight to the issuer ACS. The
 *   ACS reports the outcome to Cardinal out of band (RReq) — authentication-results is final before
 *   the browser leg ends — and then delivers the CRes to Cardinal's TermURL, which answers the frame
 *   with a cosmetic 400 page instead of redirecting to our return URL (probed 2026-08-06; see
 *   PAYER-AUTH-PLAN.md). Completion is therefore detected from that page's terminal postMessage.
 *
 * Stateless: no session, quote, customer, or request parameters are read.
 *
 * The page body is built here rather than from a template on purpose. It has no theming, no layout,
 * and no store data, and a merchant theme override of it would break 3DS at the ACS step with no
 * visible symptom. The tradeoff is that the script below sits outside the JS lint/build path: treat
 * it as a fixed protocol stub, and put anything that needs real maintenance in payer-auth-client.js.
 */
class Challenge implements CspAwareActionInterface, HttpGetActionInterface
{
    /**
     * Challenge relay. Static except for the protocol tag.
     */
    private const SCRIPT = <<<'JS'
(function () {
    'use strict';

    var TAG = '{{tag}}';
    var ORIGIN = window.location.origin;
    var frame = document.getElementById('pl-pa-acs');
    var started = false;
    var relayed = false;

    /**
     * True when the value is a non-empty https: URL string.
     *
     * @param {*} url
     * @return {boolean}
     */
    function isHttpsUrl(url) {
        var parsed;

        if (typeof url !== 'string' || url === '') {
            return false;
        }

        try {
            parsed = new URL(url);
        } catch (e) {
            return false;
        }

        return parsed.protocol === 'https:';
    }

    /**
     * Form-POST one hidden field into our child iframe.
     *
     * @param {string} action
     * @param {string} name
     * @param {string} value
     * @return {void}
     */
    function submitToFrame(action, name, value) {
        var form = document.createElement('form');
        var input = document.createElement('input');

        form.setAttribute('method', 'POST');
        form.setAttribute('action', action);
        form.setAttribute('target', 'pl-pa-acs');
        form.style.display = 'none';

        input.setAttribute('type', 'hidden');
        input.setAttribute('name', name);
        input.value = value;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    /**
     * Start the challenge in whichever transport the enrollment reply supplied: the Cardinal-hosted
     * step-up frame (JWT=<accessToken> to stepUpUrl) when its pair is complete, else the raw EMV
     * shape (creq=<pareq> to the issuer ACS).
     *
     * @param {*} stepUpUrl
     * @param {*} jwt
     * @param {*} acsUrl
     * @param {*} pareq
     * @return {void}
     */
    function startChallenge(stepUpUrl, jwt, acsUrl, pareq) {
        if (started) {
            return;
        }

        if (isHttpsUrl(stepUpUrl) && typeof jwt === 'string' && jwt !== '') {
            started = true;
            submitToFrame(stepUpUrl, 'JWT', jwt);
        } else if (isHttpsUrl(acsUrl) && typeof pareq === 'string' && pareq !== '') {
            started = true;
            submitToFrame(acsUrl, 'creq', pareq);
        }
    }

    /**
     * Relay the challenge completion up to the checkout, once.
     *
     * @return {void}
     */
    function relayReturn() {
        if (relayed || !window.parent || window.parent === window) {
            return;
        }

        relayed = true;
        window.parent.postMessage({source: TAG, event: 'return'}, ORIGIN);
    }

    /**
     * True when a child-frame message is the Cardinal TermURL's terminal signal.
     *
     * In the raw EMV transport the CRes lands on Cardinal's TermURL, which does not redirect the
     * frame to our return URL -- it serves a page that postMessages a JSON STRING (e.g.
     * '{"ErrorNumber":4100,"Message":"Unable to complete transaction"}') to its parent with
     * origin '*'. By then the ACS has already reported the outcome to Cardinal out of band, so
     * authentication-results is final behind that exact page (probed 2026-08-06; see
     * PAYER-AUTH-PLAN.md). The signal is pinned to "string that parses to a JSON object":
     * object-shaped messages are NOT terminal, because issuer ACS pages post structured
     * resize/heartbeat events mid-challenge, and a forged or early string costs only a finalize
     * round-trip -- finalize re-reads the outcome from CyberSource.
     *
     * @param {*} data
     * @return {boolean}
     */
    function isTerminalChildMessage(data) {
        var parsed;

        if (typeof data !== 'string' || data === '') {
            return false;
        }

        try {
            parsed = JSON.parse(data);
        } catch (e) {
            return false;
        }

        return parsed !== null && typeof parsed === 'object';
    }

    /**
     * Message router. Trust rules, deliberately different per event:
     *
     * - 'challenge' comes from the checkout, which is same-origin with this page, so the origin is
     *   checked strictly. Anything else is ignored.
     * - 'return' comes from our own return page, but only after a chain of issuer-controlled
     *   redirects, so its origin is not portably knowable and cannot be checked. It is instead
     *   identified by source window (it must be our own ACS child frame), and it carries no data --
     *   the event is the entire signal, and finalize re-reads the outcome from CyberSource.
     * - the terminal JSON string comes from Cardinal's TermURL page in the same child frame (see
     *   isTerminalChildMessage); it is the completion signal for the raw EMV transport.
     *
     * @param {MessageEvent} event
     * @return {void}
     */
    function onMessage(event) {
        var data = event.data;

        if (frame && event.source === frame.contentWindow && isTerminalChildMessage(data)) {
            relayReturn();

            return;
        }

        if (!data || data.source !== TAG) {
            return;
        }

        if (data.event === 'challenge' && event.origin === ORIGIN && event.source === window.parent) {
            startChallenge(data.stepUpUrl, data.jwt, data.acsUrl, data.pareq);
        } else if (data.event === 'return' && frame && event.source === frame.contentWindow) {
            relayReturn();
        }
    }

    window.addEventListener('message', onMessage, false);

    if (window.parent && window.parent !== window) {
        window.parent.postMessage({source: TAG, event: 'ready'}, ORIGIN);
    }
}());
JS;

    /**
     * Rendered document. No request data and no store data reach it; see execute() for what is bound.
     */
    private const BODY = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="robots" content="noindex,nofollow">
<title>Payment Authentication</title>
<style>
html,body{margin:0;padding:0;height:100%;background:#fff}
#pl-pa-acs{display:block;border:0;width:100%;height:100%}
</style>
</head>
<body>
<iframe id="pl-pa-acs" name="pl-pa-acs" title="Payment Authentication"></iframe>
{{script}}
</body>
</html>
HTML;

    /**
     * Challenge constructor.
     *
     * @param ResultFactory $resultFactory
     * @param SecureHtmlRenderer $secureRenderer
     */
    public function __construct(
        private readonly ResultFactory $resultFactory,
        private readonly SecureHtmlRenderer $secureRenderer
    ) {
    }

    /**
     * Emit the wrapper document. Request parameters are ignored entirely.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Raw $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $result->setContents($this->renderBody());

        return $result;
    }

    /**
     * Bind the protocol tag into the document.
     *
     * The script tag goes through SecureHtmlRenderer rather than being written out here, so the CSP
     * module can secure it the same way it secures a template's inline script: a nonce on 2.4.7+,
     * a sha256 hash on 2.4.6, and nothing at all while the policy still permits 'unsafe-inline'.
     *
     * @return string
     */
    private function renderBody(): string
    {
        $script = strtr(self::SCRIPT, ['{{tag}}' => MessageProtocol::MESSAGE_TAG]);

        return strtr(self::BODY, ['{{script}}' => $this->secureRenderer->renderTag('script', [], $script, false)]);
    }

    /**
     * Relax framing and form submission for this route only.
     *
     * Magento's default storefront policy sets both frame-src and form-action to 'self' alone, which
     * blocks the challenge iframe and its form POST in either transport. The child frame is either
     * Cardinal's step-up URL or the issuer ACS directly, and both navigate through issuer-controlled
     * pages, which are unbounded, so no host list is possible -- the https: scheme source is the
     * tightest workable grant. Returned policies merge by id, so 'self' and any whitelisted hosts
     * are preserved; the policy applies only to this controller
     * (Magento\Csp\Model\Collector\ControllerCollector), so the checkout page's CSP is untouched.
     *
     * @param \Magento\Csp\Api\Data\PolicyInterface[] $appliedPolicies
     * @return \Magento\Csp\Api\Data\PolicyInterface[]
     */
    public function modifyCsp(array $appliedPolicies): array
    {
        $appliedPolicies[] = new FetchPolicy('frame-src', false, [], ['https'], true);
        $appliedPolicies[] = new FetchPolicy('form-action', false, [], ['https'], true);

        return $appliedPolicies;
    }
}
