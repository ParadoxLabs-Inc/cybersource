<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APOptionsOption
{
    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var int $data
     */
    protected $data;

    /**
     * @param int $data
     */
    public function __construct($data)
    {
        $this->data = $data;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsOption
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsOption
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param int $data
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsOption
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
