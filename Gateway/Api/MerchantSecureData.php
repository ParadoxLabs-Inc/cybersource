<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class MerchantSecureData
{
    /**
     * @var string $field1
     */
    protected $field1;

    /**
     * @var string $field2
     */
    protected $field2;

    /**
     * @var string $field3
     */
    protected $field3;

    /**
     * @var string $field4
     */
    protected $field4;

    /**
     * @return string
     */
    public function getField1()
    {
        return $this->field1;
    }

    /**
     * @param string $field1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MerchantSecureData
     */
    public function setField1($field1)
    {
        $this->field1 = $field1;

        return $this;
    }

    /**
     * @return string
     */
    public function getField2()
    {
        return $this->field2;
    }

    /**
     * @param string $field2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MerchantSecureData
     */
    public function setField2($field2)
    {
        $this->field2 = $field2;

        return $this;
    }

    /**
     * @return string
     */
    public function getField3()
    {
        return $this->field3;
    }

    /**
     * @param string $field3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MerchantSecureData
     */
    public function setField3($field3)
    {
        $this->field3 = $field3;

        return $this;
    }

    /**
     * @return string
     */
    public function getField4()
    {
        return $this->field4;
    }

    /**
     * @param string $field4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MerchantSecureData
     */
    public function setField4($field4)
    {
        $this->field4 = $field4;

        return $this;
    }
}
