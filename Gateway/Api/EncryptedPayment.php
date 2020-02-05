<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class EncryptedPayment
{

    /**
     * @var string $descriptor
     */
    protected $descriptor = null;

    /**
     * @var string $data
     */
    protected $data = null;

    /**
     * @var string $encoding
     */
    protected $encoding = null;

    /**
     * @var string $wrappedKey
     */
    protected $wrappedKey = null;

    /**
     * @var int $referenceID
     */
    protected $referenceID = null;

    /**
     * @var string $errorCode
     */
    protected $errorCode = null;

    /**
     * @var string $keySerialNumber
     */
    protected $keySerialNumber = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getDescriptor()
    {
      return $this->descriptor;
    }

    /**
     * @param string $descriptor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setDescriptor($descriptor)
    {
      $this->descriptor = $descriptor;
      return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
      return $this->data;
    }

    /**
     * @param string $data
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setData($data)
    {
      $this->data = $data;
      return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
      return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setEncoding($encoding)
    {
      $this->encoding = $encoding;
      return $this;
    }

    /**
     * @return string
     */
    public function getWrappedKey()
    {
      return $this->wrappedKey;
    }

    /**
     * @param string $wrappedKey
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setWrappedKey($wrappedKey)
    {
      $this->wrappedKey = $wrappedKey;
      return $this;
    }

    /**
     * @return int
     */
    public function getReferenceID()
    {
      return $this->referenceID;
    }

    /**
     * @param int $referenceID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setReferenceID($referenceID)
    {
      $this->referenceID = $referenceID;
      return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
      return $this->errorCode;
    }

    /**
     * @param string $errorCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setErrorCode($errorCode)
    {
      $this->errorCode = $errorCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getKeySerialNumber()
    {
      return $this->keySerialNumber;
    }

    /**
     * @param string $keySerialNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\EncryptedPayment
     */
    public function setKeySerialNumber($keySerialNumber)
    {
      $this->keySerialNumber = $keySerialNumber;
      return $this;
    }

}
