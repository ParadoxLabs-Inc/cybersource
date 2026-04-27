<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class accountHolder
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \ParadoxLabs\CyberSource\Gateway\Api\accountHolder
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
