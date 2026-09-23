<?php declare(strict_types=1);
/**
 * Copyright © 2026-present ParadoxLabs, Inc.
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

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout;

/**
 * Recognizes legacy Secure Storage tokens (16 or 22 digits), which cards saved before 4.0.0 can hold.
 *
 * TMS tokens are 32 hex characters, so the shape alone tells them apart. Legacy tokens must be sent as
 * paymentInformation.legacyToken.id (payments) or paymentInformation.customer.customerId (Payer Auth);
 * sent as a paymentInstrument.id they draw INVALID_REQUEST/INVALID_DATA.
 */
trait LegacyTokenTrait
{
    /**
     * @param string|null $tokenId Card paymentId.
     * @return bool
     */
    protected function isLegacyToken(?string $tokenId): bool
    {
        return $tokenId !== null
            && ctype_digit($tokenId)
            && in_array(strlen($tokenId), [16, 22], true);
    }
}
