<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class DirectDebitValidateService
{
    /**
     * @var string $directDebitValidateText
     */
    protected $directDebitValidateText;

    /**
     * @param boolean $run
     */
    public function __construct(protected $run)
    {
    }

    /**
     * @return string
     */
    public function getDirectDebitValidateText()
    {
        return $this->directDebitValidateText;
    }

    /**
     * @param string $directDebitValidateText
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitValidateService
     */
    public function setDirectDebitValidateText($directDebitValidateText)
    {
        $this->directDebitValidateText = $directDebitValidateText;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\DirectDebitValidateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
