<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model\Service\SecureAcceptance;

use ParadoxLabs\CyberSource\Model\Config\Config;

/**
 * SecureAcceptance Class
 */
class Response
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac
     */
    protected $hmac;

    /**
     * @var \ParadoxLabs\TokenBase\Api\CardRepositoryInterface
     */
    protected $cardRepository;

    /**
     * @var \ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory
     */
    protected $cardFactory;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Source\CardType
     */
    protected $cardType;

    /**
     * @var \ParadoxLabs\TokenBase\Helper\Address
     */
    protected $addressHelper;

    /**
     * SecureAcceptance constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory $cardFactory
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \ParadoxLabs\CyberSource\Model\Source\CardType $cardType
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac $hmac,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory $cardFactory,
        \ParadoxLabs\CyberSource\Helper\Data $helper,
        \ParadoxLabs\CyberSource\Model\Source\CardType $cardType,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper
    ) {
        $this->hmac = $hmac;
        $this->cardRepository = $cardRepository;
        $this->cardFactory = $cardFactory;
        $this->helper = $helper;
        $this->cardType = $cardType;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Save and return a stored card for the given Secure Acceptance response
     *
     * @param array $input
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface
     */
    public function saveCard($input)
    {
        $input['signature_validation'] = $this->hmac->validateSignature($input);

        $this->helper->log(
            Config::CODE,
            json_encode($input)
        );

        $this->validateRequest($input);

        /** @var \ParadoxLabs\CyberSource\Model\Card $card */
        $card = $this->getCard($input);
        $card->setPaymentId(isset($input['payment_token']) ? $input['payment_token'] : $input['req_payment_token']);
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
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Exception
     */
    protected function getAddress($input)
    {
        $addressArray = [
            'firstname' => isset($input['req_bill_to_forename']) ? $input['req_bill_to_forename'] : null,
            'lastname' => isset($input['req_bill_to_surname']) ? $input['req_bill_to_surname'] : null,
            'company' => isset($input['req_bill_to_company_name']) ? $input['req_bill_to_company_name'] : null,
            'street' => [
                isset($input['req_bill_to_address_line1']) ? $input['req_bill_to_address_line1'] : null,
                isset($input['req_bill_to_address_line2']) ? $input['req_bill_to_address_line2'] : null,
            ],
            'city' => isset($input['req_bill_to_address_city']) ? $input['req_bill_to_address_city'] : null,
            'region' => isset($input['req_bill_to_address_state']) ? $input['req_bill_to_address_state'] : null,
            'region_code' => isset($input['req_bill_to_address_state']) ? $input['req_bill_to_address_state'] : null,
            'postcode' => isset($input['req_bill_to_address_postal_code'])
                ? $input['req_bill_to_address_postal_code']
                : null,
            'country_id' => isset($input['req_bill_to_address_country']) ? $input['req_bill_to_address_country'] : null,
            'telephone' => isset($input['req_bill_to_phone']) ? $input['req_bill_to_phone'] : null,
        ];

        return $this->addressHelper->buildAddressFromInput($addressArray);
    }

    /**
     * Set card payment data from Secure Acceptance response params
     *
     * @param array $input
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return void
     * @throws \Exception
     */
    protected function setCardPaymentInfo($input, \ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
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
     * @throws \Magento\Framework\Exception\SecurityViolationException
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validateRequest($input)
    {
        if ($input['signature_validation'] !== true) {
            throw new \Magento\Framework\Exception\SecurityViolationException(
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

            throw new \Magento\Framework\Exception\InputException($message);
        }
    }

    /**
     * Get or create a TokenBase Card for the given Secure Acceptance response
     *
     * @param array $input
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface
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
            } catch (\Exception $exception) {
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
