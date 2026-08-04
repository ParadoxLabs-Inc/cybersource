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

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Payer authentication challenge return target (the ACS 'returnUrl').
 *
 * The issuer's ACS delivers the CRes here by cross-origin form POST (some implementations use a GET
 * redirect instead), inside the challenge iframe. This action is deliberately inert: it reads NOTHING
 * from the request, touches no session, quote, or customer, and echoes nothing back. The persisted
 * payer-auth record is authoritative and the finalize call re-reads the outcome from CyberSource, so
 * the CRes payload -- including its transaction id -- has no value here and is never interpreted.
 *
 * All it does is signal "the challenge frame came back" to the wrapper page that frames it.
 */
class Callback implements CsrfAwareActionInterface, HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Rendered document. Static: no request data, no store data, nothing to escape.
     *
     * The postMessage target origin is '*' by deliberate design. The parent IS our own wrapper page,
     * but this document is reached through a chain of issuer-controlled redirects and cannot portably
     * know the origin it was framed from. That is safe here because the message carries NO data: the
     * event itself is the entire signal, and the wrapper additionally verifies the source window.
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
<p>You may close this window.</p>
<script>
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

        window.parent.postMessage({source: 'pl-cybersource-payerauth', event: 'return'}, '*');
    }

    try {
        notifyParent();
    } catch (e) {
        // Nothing actionable: the wrapper falls back to its own challenge timeout.
    }
}());
</script>
</body>
</html>
HTML;

    /**
     * Callback constructor.
     *
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        private readonly ResultFactory $resultFactory
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
        $result->setContents(self::BODY);

        return $result;
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
