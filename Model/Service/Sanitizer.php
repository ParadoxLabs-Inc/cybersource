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

namespace ParadoxLabs\CyberSource\Model\Service;

use Magento\Framework\Exception\InputException;

/**
 * Sanitizer Class -- input validation and cleaning for Secure Acceptance API request fields
 */
class Sanitizer
{
    const ISO_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * JSON keys whose string values must be fully masked when logging request/response bodies.
     * This covers personal data (billTo email, phone, name, address) that must not appear in logs.
     */
    const MASKABLE_STRING_KEYS = [
        'email',
        'phoneNumber',
        'firstName',
        'lastName',
        'address1',
        'address2',
        'locality',
        'postalCode',
    ];

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
     * @throws InputException
     */
    public function email($input)
    {
        if (!empty($input) && filter_var($input, FILTER_VALIDATE_EMAIL) === false) {
            throw new InputException(__('Please enter a valid email address.'));
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
        return filter_var($input, FILTER_VALIDATE_IP) ?: null;
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

        return strlen((string) $input) >= 10 ? $this->length($input, $maxLength) : '';
    }

    /**
     * Validate URL. Will throw exception if invalid.
     *
     * @param string $input
     * @param int $maxLength
     * @return string
     * @throws InputException
     */
    public function url($input, $maxLength = 255)
    {
        if (filter_var($input, FILTER_VALIDATE_URL) === false) {
            throw new InputException(__('CyberSource gateway return URL is invalid.'));
        }

        return $this->length($input, $maxLength);
    }

    /**
     * Mask PAN, CVV, the Unified Checkout transient-token JWT, and personal data (email, phone,
     * name, address) in a JSON request/response body for secure logging.
     *
     * The card number ("number") retains only its last four digits; the security code
     * ("securityCode") is fully masked, as are the single-use credentials on the UC and payer-auth
     * traffic, since Rest logs masked request and response bodies on the error path.
     * The keys listed in self::MASKABLE_STRING_KEYS (billTo email, phone number, name,
     * and street address fields, etc.) are also fully masked, since Rest::throwOnHttpError() logs
     * maskJson() on both the request and response body. Both quoted-string and unquoted numeric JSON
     * values are redacted. Operates on the raw JSON string so the exact bytes that were transmitted
     * can be safely logged.
     *
     * @param string $json
     * @return string
     */
    public function maskJson(string $json): string
    {
        $json = (string)$json;

        // Mask card number, retaining last four digits. Matches both quoted ("number":"4111...")
        // and numeric ("number":4111...) values; numeric values are emitted as a quoted string so the
        // masked output ("************1111") remains valid JSON.
        $json = preg_replace_callback(
            '/("number"\s*:\s*)(?:"(\d+)"|(\d+))/',
            static function (array $match): string {
                $number = !empty($match[2]) ? $match[2] : ($match[3] ?? '');
                $last4  = substr($number, -4);
                $masked = str_repeat('*', max(0, strlen($number) - 4)) . $last4;

                return $match[1] . '"' . $masked . '"';
            },
            $json
        );

        // Fully mask the security code. Matches both quoted ("securityCode":"737") and numeric
        // ("securityCode":737) values; output is always a quoted "***" to keep valid JSON.
        $json = preg_replace(
            '/("securityCode"\s*:\s*)(?:"[^"]*"|\d+)/',
            '$1"***"',
            $json
        );

        // Fully mask single-use credentials. The transient token carries BOTH key spellings on the
        // wire: "transientTokenJwt" on /pts/v2/payments, "transientToken" on the payer-auth setups
        // call. The rest are payer-auth secrets on /risk/v1 replies: cavv/xid (cryptogram),
        // ucafAuthenticationData (Mastercard AAV), accessToken (DDC JWT), pareq (challenge CReq).
        // All are always quoted strings; output is a quoted "***" to keep valid JSON.
        $json = preg_replace(
            '/("(?:transientToken(?:Jwt)?|cavv|xid|ucafAuthenticationData|accessToken|pareq)"\s*:\s*)"[^"]*"/',
            '$1"***"',
            $json
        );

        // Fully mask personal data (billTo email, phone number, name, and address fields, etc. --
        // see self::MASKABLE_STRING_KEYS). These are always quoted strings; values may contain
        // escaped characters (e.g. \"), so the value pattern tolerates any escaped character or any
        // character that isn't a bare quote or backslash. Output is a quoted "***" to keep valid JSON.
        $maskableKeys = implode('|', array_map('preg_quote', static::MASKABLE_STRING_KEYS));
        $json         = preg_replace(
            '/("(?:' . $maskableKeys . ')"\s*:\s*)"(?:[^"\\\\]|\\\\.)*"/',
            '$1"***"',
            $json
        );

        return $json;
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
            if (strlen((string) $input) > 5) {
                $input = substr((string) $input, 0, 5) . '-' . substr((string) $input, 5, 4);
            } elseif (strlen((string) $input) < 5) {
                $input = str_pad((string) $input, 5, '0', STR_PAD_LEFT);
            }
        } elseif ($country === 'CA') {
            $input = preg_replace('/[^a-zA-Z0-9]/', '', $input);
            $input = substr((string) $input, 0, 3) . ' ' . substr((string) $input, 3, 3);
        }

        return $this->alphanumericPunc($input, 10);
    }
}
