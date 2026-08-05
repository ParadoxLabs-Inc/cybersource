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

use Magento\Csp\Helper\CspNonceProvider;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\MessageProtocol;

/**
 * Payer authentication challenge return target (the ACS 'returnUrl').
 *
 * The issuer's ACS delivers the CRes here by cross-origin form POST (or a GET redirect), inside the
 * challenge iframe. This action is deliberately inert: it reads NOTHING from the request, touches no
 * session, quote, or customer, and echoes nothing back. Finalize re-reads the outcome from
 * CyberSource, so the CRes payload -- transaction id included -- is never interpreted here. All this
 * does is signal "the challenge frame came back" to the wrapper page framing it.
 *
 * The page body is built here rather than from a template on purpose. It has no theming, no layout,
 * and no store data, and a merchant theme override of it would break 3DS at the ACS step with no
 * visible symptom. The tradeoff is that the script below sits outside the JS lint/build path: treat
 * it as a fixed protocol stub, and put anything that needs real maintenance in payer-auth-client.js.
 */
class Callback implements CsrfAwareActionInterface, HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Notify-the-parent stub. Static except for the protocol tag.
     *
     * The postMessage target origin is '*' by deliberate design. The parent IS our own wrapper page,
     * but this document is reached through a chain of issuer-controlled redirects and cannot portably
     * know the origin it was framed from. That is safe here because the message carries NO data: the
     * event itself is the entire signal, and the wrapper additionally verifies the source window.
     */
    private const SCRIPT = <<<'JS'
(function () {
    'use strict';

    /**
     * Tell the framing wrapper page that the issuer challenge finished. No data, by design.
     *
     * @return {void}
     */
    function notifyParent() {
        if (!window.parent || window.parent === window) {
            return;
        }

        window.parent.postMessage({source: '{{tag}}', event: 'return'}, '*');
    }

    try {
        notifyParent();
    } catch (e) {
        // Nothing actionable: the wrapper falls back to its own challenge timeout.
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
</head>
<body style="margin:0;padding:16px;font:14px/1.4 Arial,Helvetica,sans-serif">
<p>{{message}}</p>
<script{{nonce}}>
{{script}}
</script>
</body>
</html>
HTML;

    /**
     * Callback constructor.
     *
     * The nonce provider is optional because it only exists on Magento 2.4.7+, and this module
     * supports 2.4.6. Absent it, the inline script relies on the 'unsafe-inline' script-src that
     * every pre-nonce CSP configuration grants.
     *
     * @param ResultFactory $resultFactory
     * @param Escaper $escaper
     * @param CspNonceProvider|null $cspNonceProvider
     */
    public function __construct(
        private readonly ResultFactory $resultFactory,
        private readonly Escaper $escaper,
        private readonly ?CspNonceProvider $cspNonceProvider = null
    ) {
    }

    /**
     * Emit the minimal notify-the-parent document. Request content is ignored entirely.
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
     * Bind the protocol tag, the CSP nonce, and the one translatable string into the document.
     *
     * @return string
     */
    private function renderBody(): string
    {
        $script = strtr(self::SCRIPT, ['{{tag}}' => MessageProtocol::MESSAGE_TAG]);

        return strtr(
            self::BODY,
            [
                '{{nonce}}' => $this->getNonceAttribute(),
                '{{script}}' => $script,
                '{{message}}' => $this->escaper->escapeHtml(__('You may close this window.')),
            ]
        );
    }

    /**
     * Build the script tag's nonce attribute, empty when the platform has no nonce support.
     *
     * Calling generateNonce() is what adds the nonce to this response's script-src, so it must not
     * be called speculatively -- only when the attribute is actually going to be emitted.
     *
     * @return string
     */
    private function getNonceAttribute(): string
    {
        if ($this->cspNonceProvider === null) {
            return '';
        }

        return ' nonce="' . $this->escaper->escapeHtmlAttr($this->cspNonceProvider->generateNonce()) . '"';
    }

    /**
     * No custom CSRF exception: validateForCsrf() never fails.
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Exempt from form-key validation: the ACS posts here cross-origin with no session and no form key.
     *
     * Safe because the action is stateless and does nothing with the request.
     *
     * @param RequestInterface $request
     * @return bool|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
