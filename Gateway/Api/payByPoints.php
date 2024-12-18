<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class payByPoints
{
    /**
     * @var string $indicator
     */
    protected $indicator;

    /**
     * @var string $pointsBeforeRedemption
     */
    protected $pointsBeforeRedemption;

    /**
     * @var string $pointsValueBeforeRedemption
     */
    protected $pointsValueBeforeRedemption;

    /**
     * @var string $pointsRedeemed
     */
    protected $pointsRedeemed;

    /**
     * @var string $pointsValueRedeemed
     */
    protected $pointsValueRedeemed;

    /**
     * @var string $pointsAfterRedemption
     */
    protected $pointsAfterRedemption;

    /**
     * @var string $pointsValueAfterRedemption
     */
    protected $pointsValueAfterRedemption;

    /**
     * @return string
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsBeforeRedemption()
    {
        return $this->pointsBeforeRedemption;
    }

    /**
     * @param string $pointsBeforeRedemption
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setPointsBeforeRedemption($pointsBeforeRedemption)
    {
        $this->pointsBeforeRedemption = $pointsBeforeRedemption;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsValueBeforeRedemption()
    {
        return $this->pointsValueBeforeRedemption;
    }

    /**
     * @param string $pointsValueBeforeRedemption
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setPointsValueBeforeRedemption($pointsValueBeforeRedemption)
    {
        $this->pointsValueBeforeRedemption = $pointsValueBeforeRedemption;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsRedeemed()
    {
        return $this->pointsRedeemed;
    }

    /**
     * @param string $pointsRedeemed
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setPointsRedeemed($pointsRedeemed)
    {
        $this->pointsRedeemed = $pointsRedeemed;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsValueRedeemed()
    {
        return $this->pointsValueRedeemed;
    }

    /**
     * @param string $pointsValueRedeemed
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setPointsValueRedeemed($pointsValueRedeemed)
    {
        $this->pointsValueRedeemed = $pointsValueRedeemed;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsAfterRedemption()
    {
        return $this->pointsAfterRedemption;
    }

    /**
     * @param string $pointsAfterRedemption
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setPointsAfterRedemption($pointsAfterRedemption)
    {
        $this->pointsAfterRedemption = $pointsAfterRedemption;

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsValueAfterRedemption()
    {
        return $this->pointsValueAfterRedemption;
    }

    /**
     * @param string $pointsValueAfterRedemption
     * @return \ParadoxLabs\CyberSource\Gateway\Api\payByPoints
     */
    public function setPointsValueAfterRedemption($pointsValueAfterRedemption)
    {
        $this->pointsValueAfterRedemption = $pointsValueAfterRedemption;

        return $this;
    }
}
