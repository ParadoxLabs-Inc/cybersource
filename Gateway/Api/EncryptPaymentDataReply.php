<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Throwable;

class EncryptPaymentDataReply
{
    /**
     * @var string $requestDateTime
     */
    protected $requestDateTime;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptPaymentDataReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptPaymentDataReply
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
}
