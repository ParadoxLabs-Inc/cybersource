<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
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
