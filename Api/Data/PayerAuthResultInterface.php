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
 * Payer Authentication outcome, as far as a client is allowed to see it.
 *
 * This DTO carries NOTHING authorization-bearing: no CAVV/XID/ECI, no verdict internals, no
 * directory-server diagnostics. Those live only in the server-side record the money path reads.
 * `UNAVAILABLE` outcomes deliberately report as STATUS_SUCCESS — the order proceeds, just without
 * a liability shift, and the client has no business knowing the difference.
 *
 * @api
 */
interface PayerAuthResultInterface
{
    /**
     * Authenticated (or attempted/unavailable): the client may proceed to place the order.
     */
    public const STATUS_SUCCESS = 'success';

    /**
     * Authentication failed: the order must not be placed with this card.
     */
    public const STATUS_FAILED = 'failed';

    /**
     * Step-up required: render the challenge with the ACS URL and PAReq, then call finalize().
     */
    public const STATUS_CHALLENGE = 'challenge';

    /**
     * Payer Authentication did not run (disabled, card type excluded, or unsupported card).
     */
    public const STATUS_SKIPPED = 'skipped';

    /**
     * Get the outcome status: one of success, failed, challenge, skipped.
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Set the outcome status.
     *
     * @param string $status
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     */
    public function setStatus(string $status): PayerAuthResultInterface;

    /**
     * Get the issuer ACS URL the challenge iframe must form-POST to (challenge status only).
     *
     * @return string|null
     */
    public function getAcsUrl(): ?string;

    /**
     * Set the issuer ACS URL.
     *
     * @param string|null $acsUrl
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     */
    public function setAcsUrl(?string $acsUrl): PayerAuthResultInterface;

    /**
     * Get the base64 challenge request payload for the ACS form POST (challenge status only).
     *
     * @return string|null
     */
    public function getPareq(): ?string;

    /**
     * Set the challenge request payload.
     *
     * @param string|null $pareq
     * @return \ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface
     */
    public function setPareq(?string $pareq): PayerAuthResultInterface;
}
