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

namespace ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout;

/**
 * Shared scalar-to-string normalization for the Unified Checkout services.
 *
 * @see Response
 * @see CardBuilder
 */
trait StringNormalizationTrait
{
    /**
     * Normalize a scalar to a non-empty string, or null (so callers can omit empty values entirely).
     *
     * @param mixed $value
     * @return string|null
     */
    protected function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = (string)$value;

        return $string !== '' ? $string : null;
    }
}
