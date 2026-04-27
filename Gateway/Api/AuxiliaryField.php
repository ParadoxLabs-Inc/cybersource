<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

class AuxiliaryField
{
    /**
     * @param string $_
     * @param int $id
     */
    public function __construct(
        protected $_,
        protected $id,
    ) {
    }

    /**
     * @return string
     */
    public function get_()
    {
        return $this->_;
    }

    /**
     * @param string $_
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AuxiliaryField
     */
    public function set_($_)
    {
        $this->_ = $_;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return \ParadoxLabs\CyberSource\Gateway\Api\AuxiliaryField
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
