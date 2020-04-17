<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DeviceFingerprintData
{
    /**
     * @var string $data
     */
    protected $data;

    /**
     * @var string $provider
     */
    protected $provider;

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
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprintData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprintData
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DeviceFingerprintData
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
