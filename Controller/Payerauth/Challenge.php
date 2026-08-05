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
 * Same-origin wrapper page that hosts the Cardinal step-up frame for the issuer's 3DS challenge.
 *
 * The checkout frames this page and postMessages the challenge parameters to it; the page never takes
 * them from the URL, keeping the step-up JWT out of URLs, logs, and referrers. It then form-POSTs
 * JWT=<accessToken> to the Cardinal step-up URL in its own child iframe. Cardinal's frame runs the
 * CReq/CRes exchange with the ACS itself (with its own session data — the CRes never touches us; a
 * direct-to-ACS POST cannot work, because the AReq registered Cardinal's TermURL for the CRes), then
 * POSTs to our return URL, whose completion event this page relays back up to the checkout.
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
     * Form-POST the step-up JWT to Cardinal's step-up frame inside our child iframe.
     *
     * @param {string} stepUpUrl
     * @param {string} jwt
     * @return {void}
     */
    function startChallenge(stepUpUrl, jwt) {
        var parsed;
        var form;
        var input;

        if (started || typeof stepUpUrl !== 'string' || typeof jwt !== 'string') {
            return;
        }

        try {
            parsed = new URL(stepUpUrl);
        } catch (e) {
            return;
        }

        if (parsed.protocol !== 'https:') {
            return;
        }

        started = true;

        form = document.createElement('form');
        form.setAttribute('method', 'POST');
        form.setAttribute('action', stepUpUrl);
        form.setAttribute('target', 'pl-pa-acs');
        form.style.display = 'none';

        input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'JWT');
        input.value = jwt;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
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
     * Message router. Two trust rules, deliberately different:
     *
     * - 'challenge' comes from the checkout, which is same-origin with this page, so the origin is
     *   checked strictly. Anything else is ignored.
     * - 'return' comes from our own return page, but only after a chain of issuer-controlled
     *   redirects, so its origin is not portably knowable and cannot be checked. It is instead
     *   identified by source window (it must be our own ACS child frame), and it carries no data --
     *   the event is the entire signal, and finalize re-reads the outcome from CyberSource.
     *
     * @param {MessageEvent} event
     * @return {void}
     */
    function onMessage(event) {
        var data = event.data;

        if (!data || data.source !== TAG) {
            return;
        }

        if (data.event === 'challenge' && event.origin === ORIGIN && event.source === window.parent) {
            startChallenge(data.stepUpUrl, data.jwt);
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
     * blocks the step-up iframe and the JWT POST. The child frame starts at Cardinal's step-up URL
     * but then navigates through issuer-controlled ACS pages, which are unbounded, so no host list
     * is possible -- the https: scheme source is the tightest workable grant. Returned policies merge by id, so 'self' and any whitelisted hosts are preserved; the
     * policy applies only to this controller (Magento\Csp\Model\Collector\ControllerCollector), so
     * the checkout page's CSP is untouched.
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
