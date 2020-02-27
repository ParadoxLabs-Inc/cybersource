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

namespace ParadoxLabs\CyberSource\Model\Source;

/**
 * ResponseCode Class
 */
class ResponseCode
{
    /**
     * Response code messages (cleaned up for UX purposes), as the API only gives us the response code.
     * Note these are subject to be added to over time.
     * @see https://support.cybersource.com/s/article/What-does-this-response-code-mean#code_table
     */
    const RESPONSE_CODES = [
        100 => 'Transaction successful.',
        101 => 'The request is missing one or more fields.',
        102 => 'One or more fields in the request contains invalid data.',
        104 => 'Duplicate Transaction; the reference code matches one already sent in the last 15 minutes.',
        110 => 'Partial amount was approved.',
        150 => 'System failure, please try again later.',
        151 => 'The request timed out, please try again later.',
        152 => 'The request timed out, please try again later.',
        200 => 'Address verification failed. Please confirm your billing address.',
        201 => 'Credit card issuer declined the request.',
        202 => 'Invalid expiration date.',
        203 => 'Transaction declined.',
        204 => 'Transaction declined; insufficient funds.',
        205 => 'Transaction declined; the card was lost or stolen.',
        207 => 'System failure, please try again later.',
        208 => 'Transaction declined; the card is not active or unauthorized.',
        209 => 'Invalid card verification number. Please confirm your billing details.',
        210 => 'Your card has reached the credit limit.',
        211 => 'Invalid card verification number. Please confirm your billing details.',
        220 => 'Transaction declined.',
        221 => 'Transaction declined.',
        222 => 'Transaction declined; your card is frozen.',
        230 => 'Invalid card verification number. Please confirm your billing details.',
        231 => 'Invalid credit card number. Please confirm your billing details.',
        232 => 'We do not accept this card type. We apologize for the inconvenience.',
        233 => 'Transaction declined.',
        234 => 'There is a problem with the CyberSource merchant configuration.',
        235 => 'The transaction amount exceeds the originally authorized amount.',
        236 => 'System failure, please try again later.',
        237 => 'The authorization has already been reversed.',
        238 => 'The transaction has already been settled.',
        239 => 'The transaction amount must match the previous transaction amount.',
        240 => 'The card type is invalid or does not match the credit card number.',
        241 => 'The request ID is invalid.',
        242 => 'The request ID is invalid.',
        243 => 'The transaction has already been settled or reversed.',
        246 => 'The transaction cannot be voided, or has already been submitted to your processor.',
        247 => 'You cannot refund a capture that was already voided.',
        248 => 'The boleto request was declined by your processor.',
        250 => 'System failure, please try again later.',
        251 => 'The Pinless Debit card\'s use frequency or maximum amount per use has been exceeded.',
        254 => 'Your CyberSource account does not allow stand-alone refunds.',
        400 => 'Transaction declined.',
        450 => 'Apartment number missing or not found.',
        451 => 'Insufficient address information.',
        452 => 'House/Box number not found on street.',
        453 => 'Multiple address matches were found.',
        454 => 'P.O. Box identifier not found or out of range.',
        455 => 'Route service identifier not found or out of range.',
        456 => 'Street name not found in Postal code.',
        457 => 'Unknown postal code.',
        458 => 'Unable to verify address.',
        459 => 'Multiple address matches were found.',
        460 => 'Address match not found.',
        461 => 'Unsupported character set.',
        475 => 'The entered card is enrolled in Payer Authentication. Please authenticate before continuing.',
        476 => 'Encountered a Payer Authentication problem. Payer could not be authenticated.',
        480 => 'The order is marked for review by Decision Manager.',
        481 => 'Transaction declined.',
        490 => 'The aggregator or acquirer is not accepting transactions at this time.',
        491 => 'The aggregator or acquirer is not accepting this transaction.',
        520 => 'Transaction declined.',
        700 => 'Transaction declined.',
        701 => 'Export bill_country/ship_country match',
        702 => 'Export email_country match',
        703 => 'Export hostname_country/ip_country match',
    ];

    /**
     * Get (error) message for the given CyberSource response code.
     *
     * Note, the messages have been intentionally simplified for customer purposes, and in some cases useful context has
     * been removed. For instance, customer will not be told 'Declined due to fraud score'. Log will always include the
     * reference code, which can be referenced against the full response message in documentation or EBC.
     *
     * @param string|int $code
     * @return string
     */
    public function getMessage($code)
    {
        $code = (int)$code;

        return array_key_exists($code, static::RESPONSE_CODES)
            ? static::RESPONSE_CODES[ $code ]
            : 'Unknown response code';
    }
}
