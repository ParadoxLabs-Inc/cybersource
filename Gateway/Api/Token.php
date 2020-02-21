<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class Token
{

    /**
     * @var string $prefix
     */
    protected $prefix = null;

    /**
     * @var string $suffix
     */
    protected $suffix = null;

    /**
     * @var string $expirationMonth
     */
    protected $expirationMonth = null;

    /**
     * @var string $expirationYear
     */
    protected $expirationYear = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Token
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Token
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Token
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\Token
     */
    public function setExpirationYear($expirationYear)
    {
        $this->expirationYear = $expirationYear;

        return $this;
    }

}
