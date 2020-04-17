<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class APUI
{
    /**
     * @var string $colorBorder
     */
    protected $colorBorder;

    /**
     * @var string $colorBorderSelected
     */
    protected $colorBorderSelected;

    /**
     * @var string $colorButton
     */
    protected $colorButton;

    /**
     * @var string $colorButtonText
     */
    protected $colorButtonText;

    /**
     * @var string $colorCheckbox
     */
    protected $colorCheckbox;

    /**
     * @var string $colorCheckboxCheckMark
     */
    protected $colorCheckboxCheckMark;

    /**
     * @var string $colorHeader
     */
    protected $colorHeader;

    /**
     * @var string $colorLink
     */
    protected $colorLink;

    /**
     * @var string $colorText
     */
    protected $colorText;

    /**
     * @var string $borderRadius
     */
    protected $borderRadius;

    /**
     * @var string $theme
     */
    protected $theme;

    /**
     * @return string
     */
    public function getColorBorder()
    {
        return $this->colorBorder;
    }

    /**
     * @param string $colorBorder
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorBorder($colorBorder)
    {
        $this->colorBorder = $colorBorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorBorderSelected()
    {
        return $this->colorBorderSelected;
    }

    /**
     * @param string $colorBorderSelected
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorBorderSelected($colorBorderSelected)
    {
        $this->colorBorderSelected = $colorBorderSelected;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorButton()
    {
        return $this->colorButton;
    }

    /**
     * @param string $colorButton
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorButton($colorButton)
    {
        $this->colorButton = $colorButton;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorButtonText()
    {
        return $this->colorButtonText;
    }

    /**
     * @param string $colorButtonText
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorButtonText($colorButtonText)
    {
        $this->colorButtonText = $colorButtonText;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorCheckbox()
    {
        return $this->colorCheckbox;
    }

    /**
     * @param string $colorCheckbox
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorCheckbox($colorCheckbox)
    {
        $this->colorCheckbox = $colorCheckbox;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorCheckboxCheckMark()
    {
        return $this->colorCheckboxCheckMark;
    }

    /**
     * @param string $colorCheckboxCheckMark
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorCheckboxCheckMark($colorCheckboxCheckMark)
    {
        $this->colorCheckboxCheckMark = $colorCheckboxCheckMark;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorHeader()
    {
        return $this->colorHeader;
    }

    /**
     * @param string $colorHeader
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorHeader($colorHeader)
    {
        $this->colorHeader = $colorHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorLink()
    {
        return $this->colorLink;
    }

    /**
     * @param string $colorLink
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorLink($colorLink)
    {
        $this->colorLink = $colorLink;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorText()
    {
        return $this->colorText;
    }

    /**
     * @param string $colorText
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setColorText($colorText)
    {
        $this->colorText = $colorText;

        return $this;
    }

    /**
     * @return string
     */
    public function getBorderRadius()
    {
        return $this->borderRadius;
    }

    /**
     * @param string $borderRadius
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setBorderRadius($borderRadius)
    {
        $this->borderRadius = $borderRadius;

        return $this;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     * @return \ParadoxLabs\CyberSource\Gateway\Api\APUI
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }
}
