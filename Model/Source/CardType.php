<?php
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
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Source;

/**
 * CardType Class
 */
class CardType
{
    const CARD_TYPE_MAP = [
        '001' => 'VI',
        '002' => 'MC',
        '003' => 'AE',
        '004' => 'DI',
        '005' => 'DN',
        '007' => 'JCB',
        '042' => 'MI',
    ];

    /**
     * Get Magento card type for the given CyberSource type code.
     *
     * @param $code
     * @return mixed|string
     */
    public function getType($code)
    {
        return array_key_exists($code, static::CARD_TYPE_MAP)
            ? static::CARD_TYPE_MAP[$code]
            : 'OT';
    }
}
