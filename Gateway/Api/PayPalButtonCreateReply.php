<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Throwable;

class PayPalButtonCreateReply
{
    /**
     * @var string $encryptedFormData
     */
    protected $encryptedFormData;

    /**
     * @var string $unencryptedFormData
     */
    protected $unencryptedFormData;

    /**
     * @var string $requestDateTime
     */
    protected $requestDateTime;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

    /**
     * @var string $buttonType
     */
    protected $buttonType;

    /**
     * @param int $reasonCode
     */
    public function __construct(protected $reasonCode)
    {
    }

    /**
     * @return int
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param int $reasonCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncryptedFormData()
    {
        return $this->encryptedFormData;
    }

    /**
     * @param string $encryptedFormData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply
     */
    public function setEncryptedFormData($encryptedFormData)
    {
        $this->encryptedFormData = $encryptedFormData;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnencryptedFormData()
    {
        return $this->unencryptedFormData;
    }

    /**
     * @param string $unencryptedFormData
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply
     */
    public function setUnencryptedFormData($unencryptedFormData)
    {
        $this->unencryptedFormData = $unencryptedFormData;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDateTime()
    {
        if ($this->requestDateTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->requestDateTime);
            } catch (Throwable) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime|null $requestDateTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply
     */
    public function setRequestDateTime(?DateTime $requestDateTime = null)
    {
        if ($requestDateTime == null) {
            $this->requestDateTime = null;
        } else {
            $this->requestDateTime = $requestDateTime->format(DateTime::ATOM);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getReconciliationID()
    {
        return $this->reconciliationID;
    }

    /**
     * @param string $reconciliationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonType()
    {
        return $this->buttonType;
    }

    /**
     * @param string $buttonType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayPalButtonCreateReply
     */
    public function setButtonType($buttonType)
    {
        $this->buttonType = $buttonType;

        return $this;
    }
}
