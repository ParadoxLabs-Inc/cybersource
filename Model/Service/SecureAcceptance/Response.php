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

namespace ParadoxLabs\CyberSource\Model\Service\SecureAcceptance;

use ParadoxLabs\CyberSource\Model\Card;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\SecurityViolationException;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use Throwable;

/**
 * SecureAcceptance Class
 */
class Response
{
    /**
     * SecureAcceptance constructor.
     *
     * @param Hmac $hmac
     * @param CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory $cardFactory
     * @param Data $helper
     * @param CardType $cardType
     * @param Address $addressHelper
     */
    public function __construct(
        protected readonly Hmac $hmac,
        protected readonly CardRepositoryInterface $cardRepository,
        protected readonly CardInterfaceFactory $cardFactory,
        protected readonly Data $helper,
        protected readonly CardType $cardType,
        protected readonly Address $addressHelper
    ) {
    }

    /**
     * Save and return a stored card for the given Secure Acceptance response
     *
     * @param array $input
     * @return CardInterface
     */
    public function saveCard($input)
    {
        $input['signature_validation'] = $this->hmac->validateSignature($input);

        $this->helper->log(
            Config::CODE,
            json_encode($input)
        );

        $this->validateRequest($input);

        /** @var Card $card */
        $card = $this->getCard($input);
        $card->setPaymentId($input['payment_token'] ?? $input['req_payment_token']);
        $card->setCustomerEmail($input['req_bill_to_email']);
        $card->setCustomerId(!empty($input['req_consumer_id']) ? $input['req_consumer_id'] : null);
        $card->setCustomerIp(!empty($input['req_customer_ip_address']) ? $input['req_customer_ip_address'] : null);
        $card->setAddress($this->getAddress($input));

        $this->setCardPaymentInfo($input, $card);

        $card = $this->cardRepository->save($card);

        // Future: Persist the card on the session or quote and fetch on reload? Would be nice, rare case though.

        return $card;
    }

    /**
     * Create an address object from Secure Acceptance response params
     *
     * @param array $input
     * @return AddressInterface
     * @throws \Exception
     */
    protected function getAddress($input)
    {
        $addressArray = [
            'firstname' => $input['req_bill_to_forename'] ?? null,
            'lastname' => $input['req_bill_to_surname'] ?? null,
            'company' => $input['req_bill_to_company_name'] ?? null,
            'street' => [
                $input['req_bill_to_address_line1'] ?? null,
                $input['req_bill_to_address_line2'] ?? null,
            ],
            'city' => $input['req_bill_to_address_city'] ?? null,
            'region' => $input['req_bill_to_address_state'] ?? null,
            'region_code' => $input['req_bill_to_address_state'] ?? null,
            'postcode' => $input['req_bill_to_address_postal_code'] ?? null,
            'country_id' => $input['req_bill_to_address_country'] ?? null,
            'telephone' => $input['req_bill_to_phone'] ?? null,
        ];

        return $this->addressHelper->buildAddressFromInput($addressArray);
    }

    /**
     * Set card payment data from Secure Acceptance response params
     *
     * @param array $input
     * @param CardInterface $card
     * @return void
     * @throws \Exception
     */
    protected function setCardPaymentInfo($input, CardInterface $card)
    {
        $yr = substr((string)$input['req_card_expiry_date'], -4);
        $mo = substr((string)$input['req_card_expiry_date'], 0, 2);

        $day = date('t', strtotime($yr . '-' . $mo));
        $card->setExpires(sprintf('%s-%s-%s 23:59:59', $yr, $mo, $day));

        // Sometimes the full number is masked. idk. But instrument ID should have the same last4.
        $last4 = substr((string)$input['req_card_number'], -4);
        if ($last4 === 'xxxx' && !empty($input['payment_token_instrument_identifier_id'])) {
            $last4 = substr((string)$input['payment_token_instrument_identifier_id'], -4);
        }

        $card->setAdditional('cc_type', $this->cardType->getType($input['req_card_type']));
        $card->setAdditional('cc_last4', $last4);
        $card->setAdditional('cc_bin', substr((string)$input['req_card_number'], 0, 6));
        $card->setAdditional('cc_exp_year', $yr);
        $card->setAdditional('cc_exp_month', $mo);

        if (!empty($input['auth_avs_code'])) {
            $card->setAdditional('auth_avs_code', $input['auth_avs_code']);
        }
        if (!empty($input['auth_trans_ref_no'])) {
            $card->setAdditional('auth_transaction_id', $input['auth_trans_ref_no']);
        }

        // NB: This is a card fingerprint, unique to each credit card but not reversible. Last4 will match the CC.
        if (!empty($input['payment_token_instrument_identifier_id'])) {
            $card->setAdditional('instrument_identifier', $input['payment_token_instrument_identifier_id']);
            $card->setAdditional('fingerprint', $input['payment_token_instrument_identifier_id']);
        }
    }

    /**
     * Validate the CyberSource response data for error conditions.
     *
     * Any exceptions thrown here are caught at view/frontend/templates/secure-acceptance/complete.phtml:21
     * and passed to the form JS for processing/display.
     *
     * @param array $input
     * @return void
     * @throws SecurityViolationException
     * @throws InputException
     */
    protected function validateRequest($input)
    {
        if ($input['signature_validation'] !== true) {
            throw new SecurityViolationException(
                __('Invalid request signature.')
            );
        }

        if (in_array($input['decision'], ['DECLINE', 'ERROR', 'CANCEL', 'REVIEW'], true) === true) {
            if (!empty($input['message']) && !empty($input['invalid_fields'])) {
                $input['message'] .= ': ' . $input['invalid_fields'];
            }

            $prompt = $input['decision'] === 'ERROR'
                ? 'An error occurred'
                : 'Credit card was not accepted';

            $message = !empty($input['message'])
                ? __($prompt . ': %1 (%2)', __($input['message']), $input['reason_code'])
                : __($prompt . '. (%1)', $input['reason_code']);

            throw new InputException($message);
        }
    }

    /**
     * Get or create a TokenBase Card for the given Secure Acceptance response
     *
     * @param array $input
     * @return CardInterface
     */
    protected function getCard($input)
    {
        if (!empty($input['req_merchant_defined_data99'])) {
            try {
                $card = $this->cardRepository->getByHash($input['req_merchant_defined_data99']);

                if ($card->getMethod() === Config::CODE
                    && (int)$card->getCustomerId() === (int)$input['req_consumer_id']) {
                    return $card;
                }
            } catch (Throwable) {
                // If card failed to load, store it as a new one instead.
            }
        }

        $card = $this->cardFactory->create();
        $card->setMethod(Config::CODE);
        $card->setActive(0);

        if (!empty($input['req_merchant_defined_data100'])
            && $input['req_merchant_defined_data100'] === 'paymentinfo') {
            $card->setActive(1);
        }

        return $card;
    }
}
