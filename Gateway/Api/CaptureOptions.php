<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class CaptureOptions
{
    /**
     * @var string $notes
     */
    protected $notes;

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\CaptureOptions
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }
}
