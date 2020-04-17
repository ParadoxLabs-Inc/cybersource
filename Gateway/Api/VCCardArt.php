<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class VCCardArt
{
    /**
     * @var string $fileName
     */
    protected $fileName;

    /**
     * @var string $height
     */
    protected $height;

    /**
     * @var string $width
     */
    protected $width;

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCardArt
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCardArt
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCardArt
     */
    public function setWidth($width)
    {
        $this->width = $width;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\VCCardArt
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
