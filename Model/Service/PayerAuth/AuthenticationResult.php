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

/**
 * Immutable result of a /risk/v1/authentications or /risk/v1/authentication-results call.
 *
 * Carries the classified Verdict plus the normalized consumerAuthenticationInformation block from
 * the reply. This object is SERVER-SIDE ONLY: the `ca` block holds the CAVV/XID and must never be
 * handed to a client DTO or written to a log.
 */
class AuthenticationResult
{
    /**
     * AuthenticationResult constructor.
     *
     * @param Verdict $verdict
     * @param array<string, mixed> $consumerAuthenticationInformation
     */
    public function __construct(
        public readonly Verdict $verdict,
        public readonly array $consumerAuthenticationInformation = []
    ) {
    }

    /**
     * Get the classified outcome.
     *
     * @return Verdict
     */
    public function getVerdict(): Verdict
    {
        return $this->verdict;
    }

    /**
     * Get the normalized consumerAuthenticationInformation block from the reply.
     *
     * @return array<string, mixed>
     */
    public function getConsumerAuthenticationInformation(): array
    {
        return $this->consumerAuthenticationInformation;
    }

    /**
     * Get the authentication transaction id (needed to finalize a challenge via authentication-results).
     *
     * @return string|null
     */
    public function authenticationTransactionId(): ?string
    {
        return $this->stringValue('authenticationTransactionId');
    }

    /**
     * Get the issuer ACS URL (CHALLENGE only).
     *
     * The raw-EMV challenge transport: the browser form-POSTs creq=<pareq> here. Direct-API-shaped
     * accounts (this MID included; probed 2026-08-06) return ONLY this pair — no stepUpUrl or
     * accessToken. The AReq registered Cardinal's TermURL for the CRes, and that endpoint answers
     * the frame with a cosmetic 400 rather than redirecting to our return URL, but the ACS has
     * already reported the outcome to Cardinal out of band, so authentication-results is final —
     * completion is detected from that page's terminal postMessage (see Controller/Payerauth/
     * Challenge).
     *
     * @return string|null
     */
    public function acsUrl(): ?string
    {
        return $this->stringValue('acsUrl');
    }

    /**
     * Get the base64 CReq payload (CHALLENGE only); POSTed as creq to acsUrl() in the raw-EMV
     * transport.
     *
     * @return string|null
     */
    public function pareq(): ?string
    {
        return $this->stringValue('pareq');
    }

    /**
     * Get the Cardinal step-up frame URL (CHALLENGE only; hosted-step-up accounts only).
     *
     * Preferred over the raw acsUrl/pareq transport when present. Not returned by
     * Direct-API-shaped accounts — absence is a normal reply shape, not an error.
     *
     * @return string|null
     */
    public function stepUpUrl(): ?string
    {
        return $this->stringValue('stepUpUrl');
    }

    /**
     * Get the challenge-scoped step-up JWT for the step-up form POST (CHALLENGE only).
     *
     * Distinct from the setup/DDC access token. Unlike the CAVV/XID in the `ca` block, this token
     * exists to be handed to the browser: it is what Cardinal's step-up frame authenticates by.
     *
     * @return string|null
     */
    public function accessToken(): ?string
    {
        return $this->stringValue('accessToken');
    }

    /**
     * Read a scalar string value from the consumerAuthenticationInformation block.
     *
     * @param string $key
     * @return string|null
     */
    private function stringValue(string $key): ?string
    {
        $value = $this->consumerAuthenticationInformation[$key] ?? null;

        if (!is_scalar($value) || (string)$value === '') {
            return null;
        }

        return (string)$value;
    }
}
