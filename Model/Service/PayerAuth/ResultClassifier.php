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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth;

use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Config\Config;

/**
 * Classifies a decoded /risk/v1/authentications or /risk/v1/authentication-results reply.
 *
 * The rules below are pinned by the live-sandbox outcome matrix (PAYER-AUTH-PLAN.md 2.1-2.9 +
 * 2.10a, fixtures under Test/Unit/.../PayerAuth/_files). The single most important one:
 *
 *   The top-level `status` is NOT an authentication verdict. Unavailable (2.4), lookup-n/a (2.6),
 *   enrollment error (2.7), timeout (2.8) and bypass (2.9) ALL reply AUTHENTICATION_SUCCESSFUL
 *   with no CAVV. Liability shift requires paresStatus Y/A *and* a non-empty CAVV.
 */
class ResultClassifier
{
    /**
     * ResultClassifier constructor.
     *
     * @param AuthenticationResultFactory $resultFactory
     * @param Data $helper
     */
    public function __construct(
        private readonly AuthenticationResultFactory $resultFactory,
        private readonly Data $helper
    ) {
    }

    /**
     * Classify a decoded payer-auth reply into a verdict plus its normalized `ca` block.
     *
     * @param array<string, mixed> $reply Decoded authentications / authentication-results reply.
     * @return AuthenticationResult
     */
    public function classify(array $reply): AuthenticationResult
    {
        $ca      = $this->normalize($reply['consumerAuthenticationInformation'] ?? []);
        $verdict = $this->determineVerdict($reply, $ca);

        if ($verdict === Verdict::UNAVAILABLE) {
            $this->logUnavailable($reply, $ca);
        }

        /** @var AuthenticationResult $result */
        $result = $this->resultFactory->create([
            'verdict' => $verdict,
            'consumerAuthenticationInformation' => $ca,
        ]);

        return $result;
    }

    /**
     * Apply the pinned outcome rules, in order.
     *
     * @param array<string, mixed> $reply
     * @param array<string, mixed> $ca
     * @return Verdict
     */
    private function determineVerdict(array $reply, array $ca): Verdict
    {
        $status = strtoupper($this->stringValue($reply, 'status'));

        // A step-up is pending: the client must run the ACS challenge, then finalize via results.
        if ($status === 'PENDING_AUTHENTICATION') {
            return Verdict::CHALLENGE;
        }

        $paresStatus = strtoupper($this->stringValue($ca, 'paresStatus'));

        // Definitive authentication failure: hard stop, no order attempt. paresStatus N/R is a
        // failure on its own, so a reply carrying the failure only in the `ca` block (not the
        // top-level AUTHENTICATION_FAILED) cannot degrade to UNAVAILABLE.
        if ($status === 'AUTHENTICATION_FAILED' || $paresStatus === 'N' || $paresStatus === 'R') {
            return Verdict::FAILED;
        }

        // Liability shift requires the CAVV: 2.4/2.6-2.9 reply AUTHENTICATION_SUCCESSFUL without one.
        $cavv = $this->stringValue($ca, 'cavv');

        if ($cavv !== '') {
            if ($paresStatus === 'Y') {
                return Verdict::AUTHENTICATED;
            }

            if ($paresStatus === 'A') {
                return Verdict::ATTEMPTED;
            }
        }

        // paresStatus U, veresEnrolled U/B, DS errors, timeouts, missing fields, Y/A without a CAVV.
        return Verdict::UNAVAILABLE;
    }

    /**
     * Log an unavailable outcome (info level) with the directory-server diagnostics, never secrets.
     *
     * Only non-sensitive diagnostic fields are emitted: the CAVV, token, pareq and any access token
     * must never reach the log.
     *
     * @param array<string, mixed> $reply
     * @param array<string, mixed> $ca
     * @return void
     */
    private function logUnavailable(array $reply, array $ca): void
    {
        $context = [
            'status' => $this->stringValue($reply, 'status'),
            'paresStatus' => $this->stringValue($ca, 'paresStatus'),
            'veresEnrolled' => $this->stringValue($ca, 'veresEnrolled'),
            'ecommerceIndicator' => $this->stringValue($ca, 'ecommerceIndicator'),
            'authenticationTransactionId' => $this->stringValue($ca, 'authenticationTransactionId'),
            'directoryServerErrorCode' => $this->stringValue($ca, 'directoryServerErrorCode'),
            'directoryServerErrorDescription' => $this->stringValue($ca, 'directoryServerErrorDescription'),
        ];

        $details = [];
        foreach ($context as $key => $value) {
            if ($value !== '') {
                $details[] = $key . '=' . $value;
            }
        }

        $this->helper->log(
            Config::CODE,
            'Payer Authentication unavailable (no liability shift); placing without pass-through. '
            . implode(', ', $details)
        );
    }

    /**
     * Normalize the reply's consumerAuthenticationInformation block for storage/consumption.
     *
     * Scalar leaves are cast to trimmed strings (the API mixes quoted and numeric values for
     * eci/error codes); nested arrays (e.g. strongAuthentication) are kept and normalized in place;
     * anything else (objects/resources from a hostile decode) is dropped.
     *
     * @param mixed $consumerAuthenticationInformation
     * @return array<string, mixed>
     */
    private function normalize($consumerAuthenticationInformation): array
    {
        if (!is_array($consumerAuthenticationInformation)) {
            return [];
        }

        $normalized = [];

        foreach ($consumerAuthenticationInformation as $key => $value) {
            if (is_array($value)) {
                $normalized[(string)$key] = $this->normalize($value);

                continue;
            }

            if (is_bool($value)) {
                $normalized[(string)$key] = $value;

                continue;
            }

            if (is_scalar($value)) {
                $normalized[(string)$key] = trim((string)$value);
            }
        }

        return $normalized;
    }

    /**
     * Read a trimmed string value out of a decoded reply array.
     *
     * @param array<string, mixed> $data
     * @param string $key
     * @return string
     */
    private function stringValue(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? trim((string)$value) : '';
    }
}
