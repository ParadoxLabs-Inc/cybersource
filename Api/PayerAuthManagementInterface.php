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

namespace ParadoxLabs\CyberSource\Api;

use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface;

/**
 * Payer Authentication (3D Secure) for the signed-in customer's active cart.
 *
 * Three steps, in order, all against the SAME cart and the same instrument:
 *
 *  1. setup() — starts an attempt and returns the device-data-collection handles. Required: it is
 *     where the server binds the attempt to the card being authenticated.
 *  2. authenticate() — runs the enrollment check with the browser profile collected client-side.
 *  3. finalize() — only after a `challenge` result, once the issuer challenge has returned.
 *
 * The authentication result itself never leaves the server: the CAVV and its verdict are kept
 * against the quote payment and consumed at place time. A client sees a status and, on a
 * challenge, the ACS handles it needs to render the step-up.
 *
 * @api
 */
interface PayerAuthManagementInterface
{
    /**
     * Start a Payer Authentication attempt and get the device-data-collection handles.
     *
     * Exactly ONE of $transientToken (a newly entered card) or $cardHash (a stored card) must be
     * given. Stored cards are addressed by hash and resolved against the cart's customer.
     *
     * @param string|null $transientToken Unified Checkout transient token for a newly entered card.
     * @param string|null $cardHash Stored card hash.
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface
     * @throws \Magento\Framework\Exception\InputException On invalid or ambiguous input.
     * @throws \Magento\Framework\Exception\LocalizedException On any gateway or processing failure.
     */
    public function setup(?string $transientToken = null, ?string $cardHash = null): PayerAuthSetupResultInterface;

    /**
     * Run the authentication for the attempt started by setup().
     *
     * The return URL must be an absolute HTTPS URL on the store's own host; omit it to use the
     * module's own challenge-return route.
     *
     * @param \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface $browserInfo
     * @param string|null $returnUrl
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     * @throws \Magento\Framework\Exception\InputException On missing setup, browser data, or URL.
     * @throws \Magento\Framework\Exception\LocalizedException On any gateway or processing failure.
     */
    public function authenticate(
        PayerAuthBrowserInfoInterface $browserInfo,
        ?string $returnUrl = null
    ): PayerAuthResultInterface;

    /**
     * Finalize a challenged authentication after the issuer step-up has returned.
     *
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     * @throws \Magento\Framework\Exception\InputException When there is no challenge to finalize.
     * @throws \Magento\Framework\Exception\LocalizedException On any gateway or processing failure.
     */
    public function finalize(): PayerAuthResultInterface;
}
