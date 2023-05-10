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

namespace ParadoxLabs\CyberSource\Plugin\Magento\Multishipping\Block\Checkout;

/**
 * Billing Class
 */
class Billing
{
    /**
     * @var \ParadoxLabs\CyberSource\Model\Config\Config
     */
    protected $config;

    /**
     * Billing constructor.
     *
     * @param \ParadoxLabs\CyberSource\Model\Config\Config $config
     */
    public function __construct(
        \ParadoxLabs\CyberSource\Model\Config\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param \Magento\Multishipping\Block\Checkout\Billing $subject
     * @param $methods
     * @return mixed
     */
    public function afterGetMethods(
        \Magento\Multishipping\Block\Checkout\Billing $subject,
        $methods
    ) {
        /** @var \Magento\Payment\Model\MethodInterface $method */
        foreach ($methods as $key => $method) {
            /**
             * Do not allow CyberSource to be used with Multishipping checkout if Payer Auth is enabled.
             *
             * It's theoretically possible but would be substantial additional effort to do so, given the complete lack
             * of implementation overlap between standard and multishipping checkout. The bigger issue is the actual API
             * support for it, which is unclear at best. Running it for the first transaction and reusing that for the
             * remainder via PriorAuth* fields in enrollment would likely be the most plausible option--but even then
             * the way Magento attempts multishipping orders would not be conducive to our process.
             * cf. \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams::populateEnrollmentService()
             */
            if ($method->getCode() === \ParadoxLabs\CyberSource\Model\Config\Config::CODE
                && $this->config->isPayerAuthEnabled()) {
                unset($methods[$key]);
                break;
            }
        }

        return $methods;
    }
}
