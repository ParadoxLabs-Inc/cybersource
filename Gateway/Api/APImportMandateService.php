<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APImportMandateService
{
    /**
     * @var string $dateSigned
     */
    protected $dateSigned;

    /**
     * @var boolean $run
     */
    protected $run;

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
    public function getDateSigned()
    {
        return $this->dateSigned;
    }

    /**
     * @param string $dateSigned
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateService
     */
    public function setDateSigned($dateSigned)
    {
        $this->dateSigned = $dateSigned;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
