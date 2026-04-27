<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Element
{
    /**
     * @param string $infoCode
     * @param string $fieldName
     * @param int $count
     */
    public function __construct(
        protected $infoCode,
        protected $fieldName,
        protected $count,
    ) {
    }

    /**
     * @return string
     */
    public function getInfoCode()
    {
        return $this->infoCode;
    }

    /**
     * @param string $infoCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Element
     */
    public function setInfoCode($infoCode)
    {
        $this->infoCode = $infoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Element
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Element
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}
