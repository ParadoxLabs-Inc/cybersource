<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Chad Bender <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Helper;

/**
 * CyberSource Helper -- response translation maps et al.
 */
class Data extends \ParadoxLabs\TokenBase\Helper\Data
{
    // TODO: All of this

    /**
     * @var array
     */
    protected $avsResponses = [
    ];

    /**
     * @var array
     */
    protected $ccvResponses = [
    ];

    /**
     * @var array
     */
    protected $cardTypeMap = [
    ];

    /**
     * Translate AVS response codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateAvs($code)
    {
        if (isset($this->avsResponses[$code])) {
            return __(sprintf('%s (%s)', $code, $this->avsResponses[$code]));
        }

        return $code;
    }

    /**
     * Translate CCV response codes shown on admin order pages.
     *
     * @param string $code
     * @return \Magento\Framework\Phrase|string
     */
    public function translateCcv($code)
    {
        if (isset($this->ccvResponses[$code])) {
            return __(sprintf('%s (%s)', $code, $this->ccvResponses[$code]));
        }

        return $code;
    }

    /**
     * Map CC Type to Magento's.
     *
     * @param string $type
     * @return string|null
     */
    public function mapCcTypeToMagento($type)
    {
        if (!empty($type) && isset($this->cardTypeMap[$type])) {
            return $this->cardTypeMap[$type];
        }

        return null;
    }

    /**
     * Map CC Type to CyberSource's.
     *
     * @param string $type
     * @return string|null
     */
    public function mapCcTypeToGateway($type)
    {
        if (!empty($type) && in_array($type, $this->cardTypeMap)) {
            return array_search($type, $this->cardTypeMap);
        }

        return null;
    }
}
