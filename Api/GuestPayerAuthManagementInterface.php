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
 * Payer Authentication (3D Secure) for a guest cart, addressed by its masked id.
 *
 * Same three-step contract as PayerAuthManagementInterface; possession of the masked cart id IS the
 * authorization, exactly as it is for every other guest-cart endpoint. Guest carts have no stored
 * cards, so $cardHash is rejected here.
 *
 * @api
 * @see \ParadoxLabs\CyberSource\Api\PayerAuthManagementInterface
 */
interface GuestPayerAuthManagementInterface
{
    /**
     * Start a Payer Authentication attempt and get the device-data-collection handles.
     *
     * @param string $cartId Masked guest cart id.
     * @param string|null $transientToken Unified Checkout transient token for a newly entered card.
     * @param string|null $cardHash Stored card hash. Never valid on a guest cart.
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface
     * @throws \Magento\Framework\Exception\InputException On invalid or ambiguous input.
     * @throws \Magento\Framework\Exception\NoSuchEntityException When the cart does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException On any gateway or processing failure.
     */
    public function setup(
        string $cartId,
        ?string $transientToken = null,
        ?string $cardHash = null
    ): PayerAuthSetupResultInterface;

    /**
     * Run the authentication for the attempt started by setup().
     *
     * @param string $cartId Masked guest cart id.
     * @param \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface $browserInfo
     * @param string|null $returnUrl
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     * @throws \Magento\Framework\Exception\InputException On missing setup, browser data, or URL.
     * @throws \Magento\Framework\Exception\NoSuchEntityException When the cart does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException On any gateway or processing failure.
     */
    public function authenticate(
        string $cartId,
        PayerAuthBrowserInfoInterface $browserInfo,
        ?string $returnUrl = null
    ): PayerAuthResultInterface;

    /**
     * Finalize a challenged authentication after the issuer step-up has returned.
     *
     * @param string $cartId Masked guest cart id.
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     * @throws \Magento\Framework\Exception\InputException When there is no challenge to finalize.
     * @throws \Magento\Framework\Exception\NoSuchEntityException When the cart does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException On any gateway or processing failure.
     */
    public function finalize(string $cartId): PayerAuthResultInterface;
}
