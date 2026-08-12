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

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data;

use ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface;

/**
 * Device-data-collection setup result handed to the client.
 *
 * @see \ParadoxLabs\CyberSource\Api\Data\PayerAuthSetupResultInterface
 */
class SetupResult implements PayerAuthSetupResultInterface
{
    /**
     * @var bool
     */
    private bool $skipped = false;

    /**
     * @var string|null
     */
    private ?string $accessToken = null;

    /**
     * @var string|null
     */
    private ?string $deviceDataCollectionUrl = null;

    /**
     * @inheritDoc
     */
    public function getSkipped(): bool
    {
        return $this->skipped;
    }

    /**
     * @inheritDoc
     */
    public function setSkipped(bool $skipped): PayerAuthSetupResultInterface
    {
        $this->skipped = $skipped;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(?string $accessToken): PayerAuthSetupResultInterface
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDeviceDataCollectionUrl(): ?string
    {
        return $this->deviceDataCollectionUrl;
    }

    /**
     * @inheritDoc
     */
    public function setDeviceDataCollectionUrl(?string $deviceDataCollectionUrl): PayerAuthSetupResultInterface
    {
        $this->deviceDataCollectionUrl = $deviceDataCollectionUrl;

        return $this;
    }
}
