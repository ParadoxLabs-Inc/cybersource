<?php declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Gateway\Api;

use DateTime;
use Throwable;

class GiftCardVoidReply
{
    /**
     * @var string $authorizationCode
     */
    protected $authorizationCode;

    /**
     * @var string $processorResponse
     */
    protected $processorResponse;

    /**
     * @var \DateTime $requestDeTime
     */
    protected $requestDeTime;

    /**
     * @var string $reconciliationID
     */
    protected $reconciliationID;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidReply
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * @param string $authorizationCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidReply
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessorResponse()
    {
        return $this->processorResponse;
    }

    /**
     * @param string $processorResponse
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidReply
     */
    public function setProcessorResponse($processorResponse)
    {
        $this->processorResponse = $processorResponse;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRequestDeTime()
    {
        if ($this->requestDeTime == null) {
            return null;
        } else {
            try {
                return new DateTime($this->requestDeTime);
            } catch (Throwable) {
                return false;
            }
        }
    }

    /**
     * @param \DateTime|null $requestDeTime
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidReply
     */
    public function setRequestDeTime(?DateTime $requestDeTime = null)
    {
        if ($requestDeTime == null) {
            $this->requestDeTime = null;
        } else {
            $this->requestDeTime = $requestDeTime->format(DateTime::ATOM);
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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\GiftCardVoidReply
     */
    public function setReconciliationID($reconciliationID)
    {
        $this->reconciliationID = $reconciliationID;

        return $this;
    }
}
