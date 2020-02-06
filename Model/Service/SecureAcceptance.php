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

namespace ParadoxLabs\CyberSource\Model\Service;

/**
 * SecureAcceptance Class
 */
class SecureAcceptance
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Hmac
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
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory $cardFactory
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \ParadoxLabs\CyberSource\Model\Source\CardType $cardType
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac,
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
     * @param array $input
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface
     */
    public function saveCard($input)
    {
        $input['signature_validation'] = $this->hmac->validateSignature($input);

        $this->helper->log(
            'cybersource',
            json_encode($input)
        );

        if ($input['signature_validation'] !== true) {
            throw new \Magento\Framework\Exception\SecurityViolationException(
                __('Invalid request signature.')
            );
        }

        // TODO: Check for errors and stuff
        // TODO: Does it check AVS and CVV when storing? Can we make it?

        // TODO: Kick them back into the process somehow (reinit frame and pop alert?) if it fails.
        $data = [
            'avs_response' => $input['auth_avs_code'],
            'auth_code' => $input['auth_code'],
            'auth_response' => $input['auth_response'],
            'auth_transaction_id' => $input['auth_trans_ref_no'],
            'result' => $input['decision'],
            'message' => $input['message'],
            'reason_code' => $input['reason_code'],
        ];

        /** @var \ParadoxLabs\CyberSource\Model\Card $card */
        $card = $this->cardFactory->create();
        $card->setMethod('paradoxlabs_cybersource');
        $card->setProfileId($input['payment_token']); // TODO: Probably don't want to do this. Sort out these two vals.
        $card->setPaymentId($input['payment_token_instrument_identifier_id']);
        $card->setCustomerEmail($input['req_bill_to_email']);
        $card->setCustomerId(!empty($input['req_consumer_id']) ? $input['req_consumer_id'] : null);
        $card->setCustomerIp($input['req_customer_ip_address']);
        $card->setActive(0); // TODO: Will this allow checkout?

        $card->setAddress($this->getAddress($input));

        $this->setCardPaymentInfo($input, $card);

        $this->cardRepository->save($card);

        // TODO: Persist the card on the session or quote or both and fetch on reload? Would be nice, rare case though.

        return $card;
    }

    /**
     * @param array $input
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Exception
     */
    protected function getAddress($input)
    {
        $addressArray = [
            'firstname' => $input['req_bill_to_forename'],
            'lastname' => $input['req_bill_to_surname'],
            'company' => $input['req_bill_to_company_name'],
            'street' => [
                $input['req_bill_to_address_line1'],
                $input['req_bill_to_address_line2'],
            ],
            'city' => $input['req_bill_to_address_city'],
            'region' => $input['req_bill_to_address_state'], // TODO: region_code => region, region_id
            'region_code' => $input['req_bill_to_address_state'],
            'postcode' => $input['req_bill_to_address_postal_code'],
            'country_id' => $input['req_bill_to_address_country'],
            'telephone' => $input['req_bill_to_phone'],
        ];

        return $this->addressHelper->buildAddressFromInput($addressArray);
    }

    /**
     * @param array $input
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return void
     * @throws \Exception
     */
    protected function setCardPaymentInfo($input, \ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        $yr = substr($input['req_card_expiry_date'], -4);
        $mo = substr($input['req_card_expiry_date'], 0, 2);

        $day = date('t', strtotime($yr . '-' . $mo));
        $card->setExpires(sprintf('%s-%s-%s 23:59:59', $yr, $mo, $day));

        $card->setAdditional('cc_type', $this->cardType->getType($input['req_card_type']));
        $card->setAdditional('cc_last4', substr($input['req_card_number'], -4));
        $card->setAdditional('cc_bin', substr($input['req_card_number'], 0, 6));
        $card->setAdditional('cc_exp_year', $yr);
        $card->setAdditional('cc_exp_month', $mo);

        $card->setAdditional('auth_avs_code', $input['auth_avs_code']);
        $card->setAdditional('auth_transaction_id', $input['auth_trans_ref_no']);
    }
}
