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
 * Sanitizer Class -- input validation and cleaning for Secure Acceptance API request fields
 */
class Sanitizer
{
    const ISO_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * Truncate input at length
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public function length($input, $maxLength)
    {
        return substr((string)$input, 0, (int)$maxLength);
    }

    /**
     * Limit input to alphabetical characters and length
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public function alpha($input, $maxLength)
    {
        $input = preg_replace('/[^a-zA-Z]/', '', (string)$input);

        return $this->length($input, $maxLength);
    }

    /**
     * Limit input to alphanumeric characters and length
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public function alphanumeric($input, $maxLength)
    {
        $input = preg_replace('/[^a-zA-Z0-9]/', '', (string)$input);

        return $this->length($input, $maxLength);
    }

    /**
     * Limit input to alphanumeric and select special characters, and length
     *
     * See Secure Acceptance Hosted Checkout data type definitions "AlphaNumericPunctuation" type.
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public function alphanumericPunc($input, $maxLength)
    {
        $input = preg_replace('/[^a-zA-Z0-9!"#$%&\'()*+,\-.\/:;=?@^_~ ]/', '', (string)$input);

        return $this->length($input, $maxLength);
    }

    /**
     * Limit input to alphanumeric and select special characters, and length
     *
     * See Secure Acceptance Hosted Checkout data type definitions "ASCIIAlphaNumericPunctuation" type.
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public function asciiAlphanumericPunc($input, $maxLength)
    {
        $input = preg_replace('/[^a-zA-Z0-9!&\'()+,\-.\/:@]/', '', (string)$input);

        return $this->length($input, $maxLength);
    }

    /**
     * Limit input to numbers and decimals. Note the API has no support for negative amounts; any negative sign will be
     * dropped straight up. If that's not what you want, don't give a negative value.
     *
     * @param float|string|int $input
     * @return float
     */
    public function amount($input)
    {
        $input = preg_replace('/[^0-9.]/', '', (string)$input);

        return (float)$input;
    }

    /**
     * Get ISO 8601-formatted date string
     *
     * @param string $input
     * @return string
     */
    public function isoDate($input)
    {
        return date(static::ISO_FORMAT, strtotime((string)$input));
    }

    /**
     * Validate email. Will throw exception if invalid (user error).
     *
     * @param string $input
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    public function email($input)
    {
        if (!empty($input) && filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            throw new \Magento\Framework\Exception\InputException(__('Please enter a valid email address.'));
        }

        return $input;
    }

    /**
     * Validate IP. Will return null if invalid.
     *
     * @param string $input
     * @return string|null
     */
    public function ipAddress($input)
    {
        return filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ?: null;
    }

    /**
     * Limit input to digits only. Will not cast to int, in case of leading zeros.
     *
     * @param float|string|int $input
     * @param int $maxLength
     * @return string|int
     */
    public function numeric($input, $maxLength)
    {
        $input = preg_replace('/[^0-9]/', '', (string)$input);

        return $this->length($input, $maxLength);
    }

    /**
     * Limit input to ( ),+-.*#xX1234567890 characters and length.
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     */
    public function phone($input, $maxLength)
    {
        $input = preg_replace('/[^0-9xX( ),+\-.*#]/', '', (string)$input);

        return strlen($input) >= 10 ? $this->length($input, $maxLength) : '';
    }

    /**
     * Validate URL. Will throw exception if invalid.
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    public function url($input, $maxLength = 255)
    {
        if (filter_var($input, FILTER_VALIDATE_URL) === false) {
            throw new \Magento\Framework\Exception\InputException(__('CyberSource gateway return URL is invalid.'));
        }

        return $this->length($input, $maxLength);
    }

    /**
     * Enforce postal code format per country.
     *
     * @param string $input
     * @param string $country
     * @return string
     */
    public function postcode($input, $country)
    {
        $country = $this->length(strtoupper((string)$country), 2);
        $input   = (string)$input;

        if ($country === 'US') {
            $input = preg_replace('/[^\d]/', '', $input);
            if (strlen($input) > 5) {
                $input = substr($input, 0, 5) . '-' . substr($input, 5, 4);
            } elseif (strlen($input) < 5) {
                $input = str_pad($input, 5, '0', STR_PAD_LEFT);
            }
        } elseif ($country === 'CA') {
            $input = preg_replace('/[^a-zA-Z0-9]/', '', $input);
            $input = substr($input, 0, 3) . ' ' . substr($input, 3, 3);
        }

        return $this->alphanumericPunc($input, 10);
    }
}
