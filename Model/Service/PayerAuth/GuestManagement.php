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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface;
use ParadoxLabs\CyberSource\Api\GuestPayerAuthManagementInterface;

/**
 * Guest-cart Payer Authentication: resolves the masked cart id, then delegates to Management.
 *
 * Possession of the masked id is the authorization, as with every other guest-cart endpoint. The
 * cart must actually BE a guest cart: a masked id that resolves to a customer's cart is refused, so
 * this anonymous route can never operate on an account's quote (and, through it, its stored cards).
 *
 * @see \ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management
 */
class GuestManagement implements GuestPayerAuthManagementInterface
{
    /**
     * GuestManagement constructor.
     *
     * @param ManagementFactory $managementFactory
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        private readonly ManagementFactory $managementFactory,
        private readonly MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function setup(
        string $cartId,
        ?string $transientToken = null,
        ?string $cardHash = null
    ): PayerAuthSetupResultInterface {
        return $this->forCart($cartId)->setup($transientToken, $cardHash);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(
        string $cartId,
        PayerAuthBrowserInfoInterface $browserInfo,
        ?string $returnUrl = null
    ): PayerAuthResultInterface {
        return $this->forCart($cartId)->authenticate($browserInfo, $returnUrl);
    }

    /**
     * @inheritDoc
     */
    public function finalize(string $cartId): PayerAuthResultInterface
    {
        return $this->forCart($cartId)->finalize();
    }

    /**
     * Resolve the masked cart id to a guest quote and bind a Management instance to it.
     *
     * @param string $cartId
     * @return Management
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function forCart(string $cartId): Management
    {
        $quote = $this->loadGuestQuote($cartId);

        /** @var Management $management */
        $management = $this->managementFactory->create();

        return $management->setQuote($quote);
    }

    /**
     * Load the guest quote behind a masked cart id.
     *
     * @param string $cartId
     * @return CartInterface
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function loadGuestQuote(string $cartId): CartInterface
    {
        $quoteId = $this->maskedQuoteIdToQuoteId->execute($cartId);
        $quote   = $this->cartRepository->getActive($quoteId);

        if ((int)$quote->getCustomerId() > 0) {
            throw new InputException(__('No active cart was found for Payer Authentication.'));
        }

        return $quote;
    }
}
