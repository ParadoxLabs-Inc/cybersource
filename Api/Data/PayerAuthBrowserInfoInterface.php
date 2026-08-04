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
 * Browser profile the client collects for the 3-D Secure device fingerprint.
 *
 * CLIENT-COLLECTED FIELDS ONLY. User-Agent, Accept and IP address are deliberately absent: the
 * server derives them from the actual HTTP request, because a client that could set them could
 * describe a browser that never existed. Every field here is required — CyberSource silently
 * degrades an enrolled card to "not enrolled" when the browser profile is thin (gate G2), which is
 * a silent 3DS bypass, so incomplete input is rejected rather than sent.
 *
 * @api
 */
interface PayerAuthBrowserInfoInterface
{
    /**
     * Get the browser language (navigator.language, e.g. "en-US").
     *
     * @return string|null
     */
    public function getLanguage(): ?string;

    /**
     * Set the browser language.
     *
     * @param string|null $language
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setLanguage(?string $language): PayerAuthBrowserInfoInterface;

    /**
     * Get whether Java is enabled (navigator.javaEnabled()).
     *
     * @return bool|null
     */
    public function getJavaEnabled(): ?bool;

    /**
     * Set whether Java is enabled.
     *
     * @param bool|null $javaEnabled
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setJavaEnabled(?bool $javaEnabled): PayerAuthBrowserInfoInterface;

    /**
     * Get whether JavaScript is enabled (always true from a browser client).
     *
     * @return bool|null
     */
    public function getJavaScriptEnabled(): ?bool;

    /**
     * Set whether JavaScript is enabled.
     *
     * @param bool|null $javaScriptEnabled
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setJavaScriptEnabled(?bool $javaScriptEnabled): PayerAuthBrowserInfoInterface;

    /**
     * Get the screen color depth in bits (screen.colorDepth).
     *
     * @return int|null
     */
    public function getColorDepth(): ?int;

    /**
     * Set the screen color depth.
     *
     * @param int|null $colorDepth
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setColorDepth(?int $colorDepth): PayerAuthBrowserInfoInterface;

    /**
     * Get the screen height in pixels (screen.height).
     *
     * @return int|null
     */
    public function getScreenHeight(): ?int;

    /**
     * Set the screen height.
     *
     * @param int|null $screenHeight
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setScreenHeight(?int $screenHeight): PayerAuthBrowserInfoInterface;

    /**
     * Get the screen width in pixels (screen.width).
     *
     * @return int|null
     */
    public function getScreenWidth(): ?int;

    /**
     * Set the screen width.
     *
     * @param int|null $screenWidth
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setScreenWidth(?int $screenWidth): PayerAuthBrowserInfoInterface;

    /**
     * Get the UTC offset in minutes (Date::getTimezoneOffset()).
     *
     * @return int|null
     */
    public function getTimeDifference(): ?int;

    /**
     * Set the UTC offset in minutes.
     *
     * @param int|null $timeDifference
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface
     */
    public function setTimeDifference(?int $timeDifference): PayerAuthBrowserInfoInterface;
}
