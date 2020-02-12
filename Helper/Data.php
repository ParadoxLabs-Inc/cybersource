<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Helper;

/**
 * CyberSource Helper -- response translation maps et al.
 */
class Data extends \ParadoxLabs\TokenBase\Helper\Data
{
    /**
     * @var array
     * @see https://apps.cybersource.com/library/documentation/dev_guides/Secure_Acceptance_Hosted_Checkout/html/
     *  Topics/AVS_Codes.htm
     */
    protected $avsResponses = [
        'A' => 'Street matches; postcode does not',
        'B' => 'Street matches; postcode does not',
        'C' => 'Street and postcode do not match card',
        'D' => 'Match',
        'E' => 'AVS data invalid',
        'F' => 'Postcode matches; cardholder name does not',
        'H' => 'Street and postcode match; cardholder name does not',
        'I' => 'Street and postcode do not match card',
        'J' => 'Full match; chargeback protection guaranteed',
        'K' => 'Cardholder matches; street and postcode do not',
        'L' => 'Cardholder and postcode match; street does not',
        'M' => 'Match',
        'N' => 'Street and postcode do not match card',
        'O' => 'Cardholder and street match; postcode does not',
        'P' => 'Postcode matches; street does not',
        'Q' => 'Full match, but chargeback protection NOT guaranteed',
        'R' => 'AVS unavailable',
        'S' => 'AVS not supported',
        'T' => 'Street matches; cardholder name does not',
        'U' => 'AVS unavailable',
        'V' => 'Match',
        'W' => 'Postcode matches; street does not',
        'X' => 'Match',
        'Y' => 'Match',
        'Z' => 'Postcode matches; street does not',
        '1' => 'AVS not supported',
        '2' => 'AVS data invalid',
        '3' => 'Match',
        '4' => 'No match',
    ];

    /**
     * @var array
     * @see http://apps.cybersource.com/library/documentation/dev_guides/CC_Svcs_SO_API/html/Topics/app_CVN_codes.htm
     */
    protected $ccvResponses = [
        'D' => 'The transaction was considered suspicious by the issuing bank',
        'I' => 'CVN failed the processor\'s data validation',
        'M' => 'CVN matched',
        'N' => 'CVN did not match',
        'P' => 'CVN was not processed for an unspecified reason',
        'S' => 'CVN was not included in the request',
        'U' => 'Card verification is not supported by the issuing bank',
        'X' => 'Card verification is not supported by the card association',
        '1' => 'Card verification is not supported for this processor or card type',
        '2' => 'Unrecognized CVN response',
        '3' => 'No CVN response',
    ];

    /**
     * Translate AVS response codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateAvs($code)
    {
        if (isset($this->avsResponses[$code])) {
            return __(sprintf('%s (%s)', $code, $this->avsResponses[$code]));
        }

        return $code;
    }

    /**
     * Translate CCV response codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateCcv($code)
    {
        if (isset($this->ccvResponses[$code])) {
            return __(sprintf('%s (%s)', $code, $this->ccvResponses[$code]));
        }

        return $code;
    }
}
