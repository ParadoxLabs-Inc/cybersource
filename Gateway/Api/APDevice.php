<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APDevice
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var string $userAgent
     */
    protected $userAgent = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APDevice
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APDevice
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APDevice
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

}
