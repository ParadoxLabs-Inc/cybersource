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

namespace ParadoxLabs\CyberSource\Api\Data;

/**
 * Payer Authentication device-data-collection setup result.
 *
 * The setups `referenceId` is deliberately NOT exposed: it is the server's correlation handle for
 * the authentication that follows, kept in the server-side record so a client cannot substitute
 * another attempt's reference.
 *
 * @api
 */
interface PayerAuthSetupResultInterface
{
    /**
     * Whether Payer Authentication was skipped (disabled, card type excluded, or unsupported card).
     *
     * When true, no DDC is required and the client should proceed straight to placing the order.
     *
     * @return bool
     */
    public function getSkipped(): bool;

    /**
     * Set the skipped flag.
     *
     * @param bool $skipped
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface
     */
    public function setSkipped(bool $skipped): PayerAuthSetupResultInterface;

    /**
     * Get the JWT the device-data-collection iframe must POST to CyberSource.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * Set the device-data-collection access token.
     *
     * @param string|null $accessToken
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface
     */
    public function setAccessToken(?string $accessToken): PayerAuthSetupResultInterface;

    /**
     * Get the device-data-collection URL the hidden iframe posts to.
     *
     * @return string|null
     */
    public function getDeviceDataCollectionUrl(): ?string;

    /**
     * Set the device-data-collection URL.
     *
     * @param string|null $deviceDataCollectionUrl
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface
     */
    public function setDeviceDataCollectionUrl(?string $deviceDataCollectionUrl): PayerAuthSetupResultInterface;
}
