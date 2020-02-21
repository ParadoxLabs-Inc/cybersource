<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ExportService
{

    /**
     * @var string $addressOperator
     */
    protected $addressOperator = null;

    /**
     * @var string $addressWeight
     */
    protected $addressWeight = null;

    /**
     * @var string $companyWeight
     */
    protected $companyWeight = null;

    /**
     * @var string $nameWeight
     */
    protected $nameWeight = null;

    /**
     * @var string $sanctionsLists
     */
    protected $sanctionsLists = null;

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
    public function getAddressOperator()
    {
        return $this->addressOperator;
    }

    /**
     * @param string $addressOperator
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportService
     */
    public function setAddressOperator($addressOperator)
    {
        $this->addressOperator = $addressOperator;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressWeight()
    {
        return $this->addressWeight;
    }

    /**
     * @param string $addressWeight
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportService
     */
    public function setAddressWeight($addressWeight)
    {
        $this->addressWeight = $addressWeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyWeight()
    {
        return $this->companyWeight;
    }

    /**
     * @param string $companyWeight
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportService
     */
    public function setCompanyWeight($companyWeight)
    {
        $this->companyWeight = $companyWeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameWeight()
    {
        return $this->nameWeight;
    }

    /**
     * @param string $nameWeight
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportService
     */
    public function setNameWeight($nameWeight)
    {
        $this->nameWeight = $nameWeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getSanctionsLists()
    {
        return $this->sanctionsLists;
    }

    /**
     * @param string $sanctionsLists
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportService
     */
    public function setSanctionsLists($sanctionsLists)
    {
        $this->sanctionsLists = $sanctionsLists;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ExportService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }

}
