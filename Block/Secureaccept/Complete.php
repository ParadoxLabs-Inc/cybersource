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

namespace ParadoxLabs\CyberSource\Block\Secureaccept;

/**
 * Complete Class
 */
class Complete extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Service\Hmac
     */
    protected $hmac;

    /**
     * @var \ParadoxLabs\CyberSource\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\CyberSource\Model\Source\CardType
     */
    protected $cardType;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac
     * @param \ParadoxLabs\CyberSource\Helper\Data $helper
     * @param \ParadoxLabs\CyberSource\Model\Source\CardType $cardType
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \ParadoxLabs\CyberSource\Model\Service\Hmac $hmac,
        \ParadoxLabs\CyberSource\Helper\Data $helper,
        \ParadoxLabs\CyberSource\Model\Source\CardType $cardType,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->hmac = $hmac;
        $this->helper = $helper;
        $this->cardType = $cardType;
    }

    /**
     * @return array
     */
    public function getCardParams()
    {
        // TODO: Terrible way to organize this. Abstract out into a service class or something?
        $post = $this->getRequest()->getPostValue();

        $this->helper->log('cybersource', json_encode($post), true);
        $this->helper->log(
            'cybersource',
            'HMAC validation: '.($this->hmac->validateSignature($post) ? 'true' : 'false'),
            true
        );

        // TODO: Create a card with the given card data, assuming successful.
        // TODO: Kick them back into the process somehow (reinit frame and pop alert?) if it fails.
        // TODO: Pass card data back to checkout.
        $card = [
            'cc_type' => $this->cardType->getType($post['req_card_type']),
            'cc_last4' => substr($post['req_card_number'], -4),
            'cc_bin' => substr($post['req_card_number'], 0, 6),
            'cc_exp_year' => substr($post['req_card_expiry_date'], -4),
            'cc_exp_month' => substr($post['req_card_expiry_date'], 0, 2),
            'address' => [
                'firstname' => $post['req_bill_to_forename'],
                'lastname' => $post['req_bill_to_surname'],
                'email' => $post['req_bill_to_email'],
                'company' => $post['req_bill_to_company_name'],
                'street' => [
                    $post['req_bill_to_address_line1'],
                    $post['req_bill_to_address_line2'],
                ],
                'city' => $post['req_bill_to_address_city'],
                'region' => $post['req_bill_to_address_state'],
                'postcode' => $post['req_bill_to_address_postal_code'],
                'country_id' => $post['req_bill_to_address_country'],
            ],
            'avs_response' => $post['auth_avs_code'],
            'auth_code' => $post['auth_code'],
            'auth_response' => $post['auth_response'],
            'auth_transaction_id' => $post['auth_trans_ref_no'],
            'result' => $post['decision'],
            'message' => $post['message'],
            'reason_code' => $post['reason_code'],
        ];

        // TODO: Got wrong billing info out? Need to send addr in getParams request, or something

        return $card;
    }
}
