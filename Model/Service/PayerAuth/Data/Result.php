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

use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;

/**
 * Client-facing Payer Authentication outcome. Carries no authorization-bearing data.
 *
 * @see \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
 */
class Result implements PayerAuthResultInterface
{
    /**
     * @var string
     */
    private string $status = self::STATUS_SKIPPED;

    /**
     * @var string|null
     */
    private ?string $acsUrl = null;

    /**
     * @var string|null
     */
    private ?string $pareq = null;

    /**
     * @var string|null
     */
    private ?string $stepUpUrl = null;

    /**
     * @var string|null
     */
    private ?string $accessToken = null;

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): PayerAuthResultInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAcsUrl(): ?string
    {
        return $this->acsUrl;
    }

    /**
     * @inheritDoc
     */
    public function setAcsUrl(?string $acsUrl): PayerAuthResultInterface
    {
        $this->acsUrl = $acsUrl;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPareq(): ?string
    {
        return $this->pareq;
    }

    /**
     * @inheritDoc
     */
    public function setPareq(?string $pareq): PayerAuthResultInterface
    {
        $this->pareq = $pareq;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStepUpUrl(): ?string
    {
        return $this->stepUpUrl;
    }

    /**
     * @inheritDoc
     */
    public function setStepUpUrl(?string $stepUpUrl): PayerAuthResultInterface
    {
        $this->stepUpUrl = $stepUpUrl;

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
    public function setAccessToken(?string $accessToken): PayerAuthResultInterface
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
