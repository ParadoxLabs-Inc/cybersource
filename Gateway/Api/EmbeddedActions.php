<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class EmbeddedActions
{
    /**
     * @var Capture $capture
     */
    protected $capture;

    /**
     * @return Capture
     */
    public function getCapture()
    {
        return $this->capture;
    }

    /**
     * @param Capture $capture
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EmbeddedActions
     */
    public function setCapture($capture)
    {
        $this->capture = $capture;

        return $this;
    }
}
