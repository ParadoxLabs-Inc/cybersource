<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PinlessDebitService
{

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID = null;

    /**
     * @var string $commerceIndicator
     */
    protected $commerceIndicator = null;

    /**
     * @var boolean $run
     */
    protected $run = null;

    /**
     * @param boolean $run
     */
    public function __construct($run)
    {
      $this->run = $run;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
      return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitService
     */
    public function setReconciliationID($reconciliationID)
    {
      $this->reconciliationID = $reconciliationID;
      return $this;
    }

    /**
     * @return string
     */
    public function getCommerceIndicator()
    {
      return $this->commerceIndicator;
    }

    /**
     * @param string $commerceIndicator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitService
     */
    public function setCommerceIndicator($commerceIndicator)
    {
      $this->commerceIndicator = $commerceIndicator;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getRun()
    {
      return $this->run;
    }

    /**
     * @param boolean $run
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PinlessDebitService
     */
    public function setRun($run)
    {
      $this->run = $run;
      return $this;
    }

}