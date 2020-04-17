<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class TokenCustom
{
    /**
     * @var string $prefix
     */
    protected $prefix;

    /**
     * @var string $suffix
     */
    protected $suffix;

    /**
     * @var string $expirationMonth
     */
    protected $expirationMonth;

    /**
     * @var string $expirationYear
     */
    protected $expirationYear;

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenCustom
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenCustom
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationMonth()
    {
        return $this->expirationMonth;
    }

    /**
     * @param string $expirationMonth
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenCustom
     */
    public function setExpirationMonth($expirationMonth)
    {
        $this->expirationMonth = $expirationMonth;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationYear()
    {
        return $this->expirationYear;
    }

    /**
     * @param string $expirationYear
     * @return \ParadoxLabs\CyberSource\Gateway\Api\TokenCustom
     */
    public function setExpirationYear($expirationYear)
    {
        $this->expirationYear = $expirationYear;

        return $this;
    }
}
