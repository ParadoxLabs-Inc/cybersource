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

namespace ParadoxLabs\CyberSource\Model;

use Magento\Framework\Registry;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment\Transaction\Repository;
use Override;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CardBuilder;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\AbstractGateway;
use ParadoxLabs\TokenBase\Model\AbstractMethod;
use ParadoxLabs\TokenBase\Model\Gateway\Response;

/**
 * Payment method
 */
class Method extends AbstractMethod
{
    /**
     * Method constructor.
     *
     * Extends the TokenBase method with the Unified Checkout token->card mapper, used post-auth to
     * persist the inline-minted TMS ids onto the vault card (A2). All other args forward to the parent.
     *
     * @param Repository $transactionRepository
     * @param Data $helper
     * @param AbstractGateway $gateway
     * @param CardInterfaceFactory $cardFactory
     * @param CardRepositoryInterface $cardRepository
     * @param Address $addressHelper
     * @param ConfigInterface $config
     * @param Registry $registry
     * @param CardBuilder $cardBuilder
     * @param string $methodCode
     * @param array<string, mixed> $data
     */
    public function __construct(
        Repository $transactionRepository,
        Data $helper,
        AbstractGateway $gateway,
        CardInterfaceFactory $cardFactory,
        CardRepositoryInterface $cardRepository,
        Address $addressHelper,
        ConfigInterface $config,
        Registry $registry,
        protected readonly CardBuilder $cardBuilder,
        string $methodCode = '',
        array $data = []
    ) {
        parent::__construct(
            $transactionRepository,
            $helper,
            $gateway,
            $cardFactory,
            $cardRepository,
            $addressHelper,
            $config,
            $registry,
            $methodCode,
            $data
        );
    }

    /**
     * Initialize/return the API gateway class.
     *
     * @return Gateway
     * @api
     */
    #[Override]
    public function gateway()
    {
        if ($this->gateway->isInitialized() !== true) {
            $this->gateway->init(['store_id' => $this->getData('store')]);
        }

        return $this->gateway;
    }

    /**
     * Resync billing address et al. before auth/capture.
     *
     * @param InfoInterface $payment
     * @return $this
     */
    #[Override]
    protected function resyncStoredCard(InfoInterface $payment)
    {
        // All card updates are done via Unified Checkout payment responses; skip server-side resync saves.
        return $this;
    }

    /**
     * Persist the Unified Checkout TMS token onto the vault card after a successful auth.
     *
     * Runs before AbstractMethod::authorize() saves the card (legacy SA-era cards established their token
     * out-of-band, so they carry none of these keys and are untouched here). When
     * the gateway returns a UC response (identified by the token_information / uc_token_missing keys set
     * by UnifiedCheckout\Response::interpretResponse()), we map the inline-minted ids onto the loaded
     * card via CardBuilder; the surrounding authorize() flow then saves it. Token-less successes leave
     * the card un-tokenized and flagged, never throwing.
     *
     * @param InfoInterface $payment
     * @param float $amount
     * @param Response $response
     * @return void
     */
    #[Override]
    protected function afterAuthorize(
        InfoInterface $payment,
        $amount,
        Response $response
    ) {
        parent::afterAuthorize($payment, $amount, $response);

        $this->applyUnifiedCheckoutToken($response);
    }

    /**
     * Map a Unified Checkout token result onto the currently loaded card, when present.
     *
     * @param Response $response
     * @return void
     */
    protected function applyUnifiedCheckoutToken(Response $response): void
    {
        // Only act on Unified Checkout responses. The SOAP/SA path never sets these keys.
        if ($response->getData('token_information') === null
            && $response->getData('uc_token_missing') === null) {
            return;
        }

        $card = $this->getCard();
        if (!$card instanceof CardInterface) {
            return;
        }

        $this->cardBuilder->applyTokenToCard($card, $response);
    }

    /**
     * Store response statuses persistently.
     *
     * @param InfoInterface $payment
     * @param Response $response
     * @return InfoInterface
     */
    #[Override]
    protected function storeTransactionStatuses(
        InfoInterface $payment,
        Response $response
    ) {
        /** @var Payment $payment */
        if (empty($payment->getData('cc_avs_status'))) {
            $payment->setData('cc_avs_status', $response->getData('ccAuthReply.avsCode'));
        }

        if (empty($payment->getData('cc_cid_status'))) {
            $payment->setData('cc_cid_status', $response->getData('ccAuthReply.cvCode'));
        }

        if (!empty($response->getData('ccAuthReply.authorizationCode'))) {
            $payment->setData('cc_approval', $response->getData('ccAuthReply.authorizationCode'));
        }

        return $payment;
    }
}
