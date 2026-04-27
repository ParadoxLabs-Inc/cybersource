<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ECAVSService
{
    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ECAVSService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
