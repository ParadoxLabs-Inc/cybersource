<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ReplyReserved
{
    /**
     * @param string $any
     */
    public function __construct(protected $any)
    {
    }

    /**
     * @return string
     */
    public function getAny()
    {
        return $this->any;
    }

    /**
     * @param string $any
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ReplyReserved
     */
    public function setAny($any)
    {
        $this->any = $any;

        return $this;
    }
}
