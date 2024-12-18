<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class PayerAuthValidateService
{
    /**
     * @var string $signedPARes
     */
    protected $signedPARes;

    /**
     * @var string $authenticationTransactionID
     */
    protected $authenticationTransactionID;

    /**
     * @var string $responseAccessToken
     */
    protected $responseAccessToken;

    /**
     * @var string $otpToken
     */
    protected $otpToken;

    /**
     * @var boolean $credentialEncrypted
     */
    protected $credentialEncrypted;

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
    public function getSignedPARes()
    {
        return $this->signedPARes;
    }

    /**
     * @param string $signedPARes
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function setSignedPARes($signedPARes)
    {
        $this->signedPARes = $signedPARes;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthenticationTransactionID()
    {
        return $this->authenticationTransactionID;
    }

    /**
     * @param string $authenticationTransactionID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function setAuthenticationTransactionID($authenticationTransactionID)
    {
        $this->authenticationTransactionID = $authenticationTransactionID;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponseAccessToken()
    {
        return $this->responseAccessToken;
    }

    /**
     * @param string $responseAccessToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function setResponseAccessToken($responseAccessToken)
    {
        $this->responseAccessToken = $responseAccessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtpToken()
    {
        return $this->otpToken;
    }

    /**
     * @param string $otpToken
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function setOtpToken($otpToken)
    {
        $this->otpToken = $otpToken;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCredentialEncrypted()
    {
        return $this->credentialEncrypted;
    }

    /**
     * @param boolean $credentialEncrypted
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function setCredentialEncrypted($credentialEncrypted)
    {
        $this->credentialEncrypted = $credentialEncrypted;

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
     * @return \ParadoxLabs\CyberSource\Gateway\Api\PayerAuthValidateService
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }
}
