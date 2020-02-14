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
    const AVS_RESPONSE_CODES = [
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
    const CVN_RESPONSE_CODES = [
        'D' => 'Transaction considered suspicious by the issuing bank',
        'I' => 'Failed data validation',
        'M' => 'Match',
        'N' => 'No match',
        'P' => 'Not processed for an unspecified reason',
        'S' => 'No CVN provided',
        'U' => 'Not supported by the issuing bank',
        'X' => 'Not supported by the card association',
        '1' => 'Not supported for this processor or card type',
        '2' => 'Unrecognized response',
        '3' => 'No CVN response',
    ];

    /**
     * @var array
     * @see Decision Manager documentation
     */
    const RISK_FACTOR_CODES = [
        'A' => 'Excessive address changes',
        'B' => 'Card BIN or authorization risk',
        'C' => 'High number of account numbers',
        'D' => 'Email is a free provider, or otherwise risky',
        'E' => 'The customer is on your positive list',
        'F' => 'Negative list or negative history',
        'G' => 'Geolocation inconsistencies',
        'H' => 'Excessive name changes',
        'I' => 'IP address/email/billing address inconsistencies',
        'N' => 'Nonsensical input',
        'O' => 'Customer info contains obscene words',
        'P' => 'Identity morphing',
        'Q' => 'The customer’s phone number is suspicious',
        'R' => 'Risky order; multiple high-risk correlations',
        'T' => 'Purchase outside of the expected hours',
        'U' => 'Unverifiable address',
        'V' => 'Velocity',
        'W' => 'Previously marked as suspect',
        'Y' => 'Gift Order',
        'Z' => 'Invalid value; a default value was substituted',
    ];

    /**
     * Translate AVS response codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateAvs($code)
    {
        if (array_key_exists($code, static::AVS_RESPONSE_CODES)) {
            return __(sprintf('%s (%s)', $code, static::AVS_RESPONSE_CODES[ $code ]));
        }

        return $code;
    }

    /**
     * Translate CVN response codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateCvn($code)
    {
        if (array_key_exists($code, static::CVN_RESPONSE_CODES)) {
            return __(sprintf('%s (%s)', $code, static::CVN_RESPONSE_CODES[ $code ]));
        }

        return $code;
    }

    /**
     * Translate risk factor codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateRiskFactor($code)
    {
        if (array_key_exists($code, static::RISK_FACTOR_CODES)) {
            return __(sprintf('%s (%s)', $code, static::RISK_FACTOR_CODES[ $code ]));
        }

        return $code;
    }
}
