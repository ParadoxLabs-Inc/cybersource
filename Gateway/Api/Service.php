<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Service
{
    /**
     * @var string $categoryCode
     */
    protected $categoryCode;

    /**
     * @var string $subcategoryCode
     */
    protected $subcategoryCode;

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
    public function getCategoryCode()
    {
        return $this->categoryCode;
    }

    /**
     * @param string $categoryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Service
     */
    public function setCategoryCode($categoryCode)
    {
        $this->categoryCode = $categoryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubcategoryCode()
    {
        return $this->subcategoryCode;
    }

    /**
     * @param string $subcategoryCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Service
     */
    public function setSubcategoryCode($subcategoryCode)
    {
        $this->subcategoryCode = $subcategoryCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Service
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
