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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request;

use Magento\Framework\Exception\InputException;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\FilterEmptyTrait;

/**
 * Typed value object for POST /risk/v1/authentication-results (challenge finalization).
 *
 * Sent after the ACS challenge returns, to fetch the final PARes/CAVV for the authentication
 * transaction started by /risk/v1/authentications.
 */
class ResultsRequest
{
    use FilterEmptyTrait;

    /**
     * @var string|null
     */
    private ?string $clientReferenceCode = null;

    /**
     * @var string|null
     */
    private ?string $authenticationTransactionId = null;

    /**
     * Get the merchant client-reference code.
     *
     * @return string|null
     */
    public function getClientReferenceCode(): ?string
    {
        return $this->clientReferenceCode;
    }

    /**
     * Set the merchant client-reference code.
     *
     * @param string|null $clientReferenceCode
     * @return $this
     */
    public function setClientReferenceCode(?string $clientReferenceCode): self
    {
        $this->clientReferenceCode = $clientReferenceCode;

        return $this;
    }

    /**
     * Get the authentication transaction id being finalized.
     *
     * @return string|null
     */
    public function getAuthenticationTransactionId(): ?string
    {
        return $this->authenticationTransactionId;
    }

    /**
     * Set the authentication transaction id (consumerAuthenticationInformation).
     *
     * @param string|null $authenticationTransactionId
     * @return $this
     */
    public function setAuthenticationTransactionId(?string $authenticationTransactionId): self
    {
        $this->authenticationTransactionId = $authenticationTransactionId;

        return $this;
    }

    /**
     * Build the JSON-ready request tree, omitting null/empty leaves.
     *
     * @return array<string, mixed>
     * @throws InputException When the authentication transaction id is missing.
     */
    public function toArray(): array
    {
        if ($this->authenticationTransactionId === null || $this->authenticationTransactionId === '') {
            throw new InputException(
                __('Payer Authentication results require an authentication transaction id.')
            );
        }

        $request = [];

        $clientReferenceInformation = $this->filterEmpty(['code' => $this->clientReferenceCode]);
        if (!empty($clientReferenceInformation)) {
            $request['clientReferenceInformation'] = $clientReferenceInformation;
        }

        $request['consumerAuthenticationInformation'] = [
            'authenticationTransactionId' => $this->authenticationTransactionId,
        ];

        return $request;
    }
}
