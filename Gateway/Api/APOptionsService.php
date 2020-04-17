<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APOptionsService
{
    /**
     * @var string $limit
     */
    protected $limit;

    /**
     * @var string $offset
     */
    protected $offset;

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
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param string $limit
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsService
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return string
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param string $offset
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsService
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APOptionsService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
