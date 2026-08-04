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
     * Get the issuer ACS URL the challenge iframe must form-POST to (CHALLENGE only).
     *
     * @return string|null
     */
    public function acsUrl(): ?string
    {
        return $this->stringValue('acsUrl');
    }

    /**
     * Get the base64 CReq payload for the challenge form POST (CHALLENGE only).
     *
     * @return string|null
     */
    public function pareq(): ?string
    {
        return $this->stringValue('pareq');
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
