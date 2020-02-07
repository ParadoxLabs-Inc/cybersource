<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DirectDebitMandateService
{

    /**
     * @var string $mandateDescriptor
     */
    protected $mandateDescriptor = null;

    /**
     * @var string $firstDebitDate
     */
    protected $firstDebitDate = null;

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
    public function getMandateDescriptor()
    {
      return $this->mandateDescriptor;
    }

    /**
     * @param string $mandateDescriptor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateService
     */
    public function setMandateDescriptor($mandateDescriptor)
    {
      $this->mandateDescriptor = $mandateDescriptor;
      return $this;
    }

    /**
     * @return string
     */
    public function getFirstDebitDate()
    {
      return $this->firstDebitDate;
    }

    /**
     * @param string $firstDebitDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateService
     */
    public function setFirstDebitDate($firstDebitDate)
    {
      $this->firstDebitDate = $firstDebitDate;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitMandateService
     */
    public function setRun($run)
    {
      $this->run = $run;
      return $this;
    }

}
