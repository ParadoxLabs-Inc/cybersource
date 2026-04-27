<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APImportMandateService
{
    /**
     * @var string $dateSigned
     */
    protected $dateSigned;

    /**
     * @var string $setupDDInstruction
     */
    protected $setupDDInstruction;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
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
     * @return string
     */
    public function getSetupDDInstruction()
    {
        return $this->setupDDInstruction;
    }

    /**
     * @param string $setupDDInstruction
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APImportMandateService
     */
    public function setSetupDDInstruction($setupDDInstruction)
    {
        $this->setupDDInstruction = $setupDDInstruction;

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
