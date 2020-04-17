<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Subscription
{
    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $paymentMethod
     */
    protected $paymentMethod;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Subscription
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Subscription
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }
}
