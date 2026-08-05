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

namespace ParadoxLabs\CyberSource\Plugin\Magento\Multishipping\Block\Checkout;

use Magento\Multishipping\Block\Checkout\Billing as MultishippingBilling;
use Magento\Payment\Model\MethodInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;

class Billing
{
    /**
     * Billing constructor.
     *
     * @param Config $config
     */
    public function __construct(protected readonly Config $config)
    {
    }

    /**
     * Remove the payment method from multishipping checkout when Payer Authentication is enabled.
     *
     * @param MultishippingBilling $subject
     * @param array $methods
     * @return array
     */
    public function afterGetMethods(
        MultishippingBilling $subject,
        array $methods
    ): array {
        /** @var MethodInterface $method */
        foreach ($methods as $key => $method) {
            // Multishipping places one order per address, and the payer-auth ceremony binds to a
            // single quote/amount, so the method is withdrawn rather than authenticated per order.
            if ($method->getCode() === Config::CODE
                && $this->config->isPayerAuthEnabled()) {
                unset($methods[ $key ]);
                break;
            }
        }

        return $methods;
    }
}
