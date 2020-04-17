<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class MDDField
{
    /**
     * @var string $_
     */
    protected $_;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param string $_
     * @param int $id
     */
    public function __construct($_, $id)
    {
        $this->_  = $_;
        $this->id = $id;
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MDDField
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\MDDField
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
