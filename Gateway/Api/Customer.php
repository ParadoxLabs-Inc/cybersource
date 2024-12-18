<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Customer
{
    /**
     * @var string $alternatePhoneNumberVerificationStatus
     */
    protected $alternatePhoneNumberVerificationStatus;

    /**
     * @var string $alternateEmailVerificationStatus
     */
    protected $alternateEmailVerificationStatus;

    /**
     * @return string
     */
    public function getAlternatePhoneNumberVerificationStatus()
    {
        return $this->alternatePhoneNumberVerificationStatus;
    }

    /**
     * @param string $alternatePhoneNumberVerificationStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Customer
     */
    public function setAlternatePhoneNumberVerificationStatus($alternatePhoneNumberVerificationStatus)
    {
        $this->alternatePhoneNumberVerificationStatus = $alternatePhoneNumberVerificationStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateEmailVerificationStatus()
    {
        return $this->alternateEmailVerificationStatus;
    }

    /**
     * @param string $alternateEmailVerificationStatus
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Customer
     */
    public function setAlternateEmailVerificationStatus($alternateEmailVerificationStatus)
    {
        $this->alternateEmailVerificationStatus = $alternateEmailVerificationStatus;

        return $this;
    }
}
