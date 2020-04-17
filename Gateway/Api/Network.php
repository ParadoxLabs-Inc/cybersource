<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Network
{
    /**
     * @var string $octDomesticIndicator
     */
    protected $octDomesticIndicator;

    /**
     * @var string $octCrossBorderIndicator
     */
    protected $octCrossBorderIndicator;

    /**
     * @var string $aftDomesticIndicator
     */
    protected $aftDomesticIndicator;

    /**
     * @var string $aftCrossBorderIndicator
     */
    protected $aftCrossBorderIndicator;

    /**
     * @var string $networkID
     */
    protected $networkID;

    /**
     * @var string $networkOrder
     */
    protected $networkOrder;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOctDomesticIndicator()
    {
        return $this->octDomesticIndicator;
    }

    /**
     * @param string $octDomesticIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setOctDomesticIndicator($octDomesticIndicator)
    {
        $this->octDomesticIndicator = $octDomesticIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOctCrossBorderIndicator()
    {
        return $this->octCrossBorderIndicator;
    }

    /**
     * @param string $octCrossBorderIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setOctCrossBorderIndicator($octCrossBorderIndicator)
    {
        $this->octCrossBorderIndicator = $octCrossBorderIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAftDomesticIndicator()
    {
        return $this->aftDomesticIndicator;
    }

    /**
     * @param string $aftDomesticIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setAftDomesticIndicator($aftDomesticIndicator)
    {
        $this->aftDomesticIndicator = $aftDomesticIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAftCrossBorderIndicator()
    {
        return $this->aftCrossBorderIndicator;
    }

    /**
     * @param string $aftCrossBorderIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setAftCrossBorderIndicator($aftCrossBorderIndicator)
    {
        $this->aftCrossBorderIndicator = $aftCrossBorderIndicator;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkID()
    {
        return $this->networkID;
    }

    /**
     * @param string $networkID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setNetworkID($networkID)
    {
        $this->networkID = $networkID;

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkOrder()
    {
        return $this->networkOrder;
    }

    /**
     * @param string $networkOrder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setNetworkOrder($networkOrder)
    {
        $this->networkOrder = $networkOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Network
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
