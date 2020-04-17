<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ServiceFee
{
    /**
     * @var string $merchantDescriptor
     */
    protected $merchantDescriptor;

    /**
     * @var string $merchantDescriptorContact
     */
    protected $merchantDescriptorContact;

    /**
     * @var string $merchantDescriptorState
     */
    protected $merchantDescriptorState;

    /**
     * @return string
     */
    public function getMerchantDescriptor()
    {
        return $this->merchantDescriptor;
    }

    /**
     * @param string $merchantDescriptor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ServiceFee
     */
    public function setMerchantDescriptor($merchantDescriptor)
    {
        $this->merchantDescriptor = $merchantDescriptor;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorContact()
    {
        return $this->merchantDescriptorContact;
    }

    /**
     * @param string $merchantDescriptorContact
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ServiceFee
     */
    public function setMerchantDescriptorContact($merchantDescriptorContact)
    {
        $this->merchantDescriptorContact = $merchantDescriptorContact;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorState()
    {
        return $this->merchantDescriptorState;
    }

    /**
     * @param string $merchantDescriptorState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ServiceFee
     */
    public function setMerchantDescriptorState($merchantDescriptorState)
    {
        $this->merchantDescriptorState = $merchantDescriptorState;

        return $this;
    }
}
