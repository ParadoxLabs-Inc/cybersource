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

use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;

/**
 * Client-collected browser profile for the 3D Secure device fingerprint.
 *
 * @see \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
 */
class BrowserInfo implements PayerAuthBrowserInfoInterface
{
    /**
     * @var string|null
     */
    private ?string $language = null;

    /**
     * @var bool|null
     */
    private ?bool $javaEnabled = null;

    /**
     * @var bool|null
     */
    private ?bool $javaScriptEnabled = null;

    /**
     * @var int|null
     */
    private ?int $colorDepth = null;

    /**
     * @var int|null
     */
    private ?int $screenHeight = null;

    /**
     * @var int|null
     */
    private ?int $screenWidth = null;

    /**
     * @var int|null
     */
    private ?int $timeDifference = null;

    /**
     * @inheritDoc
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @inheritDoc
     */
    public function setLanguage(?string $language): PayerAuthBrowserInfoInterface
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getJavaEnabled(): ?bool
    {
        return $this->javaEnabled;
    }

    /**
     * @inheritDoc
     */
    public function setJavaEnabled(?bool $javaEnabled): PayerAuthBrowserInfoInterface
    {
        $this->javaEnabled = $javaEnabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getJavaScriptEnabled(): ?bool
    {
        return $this->javaScriptEnabled;
    }

    /**
     * @inheritDoc
     */
    public function setJavaScriptEnabled(?bool $javaScriptEnabled): PayerAuthBrowserInfoInterface
    {
        $this->javaScriptEnabled = $javaScriptEnabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getColorDepth(): ?int
    {
        return $this->colorDepth;
    }

    /**
     * @inheritDoc
     */
    public function setColorDepth(?int $colorDepth): PayerAuthBrowserInfoInterface
    {
        $this->colorDepth = $colorDepth;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getScreenHeight(): ?int
    {
        return $this->screenHeight;
    }

    /**
     * @inheritDoc
     */
    public function setScreenHeight(?int $screenHeight): PayerAuthBrowserInfoInterface
    {
        $this->screenHeight = $screenHeight;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getScreenWidth(): ?int
    {
        return $this->screenWidth;
    }

    /**
     * @inheritDoc
     */
    public function setScreenWidth(?int $screenWidth): PayerAuthBrowserInfoInterface
    {
        $this->screenWidth = $screenWidth;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTimeDifference(): ?int
    {
        return $this->timeDifference;
    }

    /**
     * @inheritDoc
     */
    public function setTimeDifference(?int $timeDifference): PayerAuthBrowserInfoInterface
    {
        $this->timeDifference = $timeDifference;

        return $this;
    }
}
