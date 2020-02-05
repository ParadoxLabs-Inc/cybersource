<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class InvoiceHeader
{

    /**
     * @var string $merchantDescriptor
     */
    protected $merchantDescriptor = null;

    /**
     * @var string $merchantDescriptorContact
     */
    protected $merchantDescriptorContact = null;

    /**
     * @var string $merchantDescriptorAlternate
     */
    protected $merchantDescriptorAlternate = null;

    /**
     * @var string $merchantDescriptorStreet
     */
    protected $merchantDescriptorStreet = null;

    /**
     * @var string $merchantDescriptorCity
     */
    protected $merchantDescriptorCity = null;

    /**
     * @var string $merchantDescriptorState
     */
    protected $merchantDescriptorState = null;

    /**
     * @var string $merchantDescriptorPostalCode
     */
    protected $merchantDescriptorPostalCode = null;

    /**
     * @var string $merchantDescriptorCountry
     */
    protected $merchantDescriptorCountry = null;

    /**
     * @var boolean $isGift
     */
    protected $isGift = null;

    /**
     * @var boolean $returnsAccepted
     */
    protected $returnsAccepted = null;

    /**
     * @var string $tenderType
     */
    protected $tenderType = null;

    /**
     * @var string $merchantVATRegistrationNumber
     */
    protected $merchantVATRegistrationNumber = null;

    /**
     * @var string $purchaserOrderDate
     */
    protected $purchaserOrderDate = null;

    /**
     * @var string $purchaserVATRegistrationNumber
     */
    protected $purchaserVATRegistrationNumber = null;

    /**
     * @var string $vatInvoiceReferenceNumber
     */
    protected $vatInvoiceReferenceNumber = null;

    /**
     * @var string $summaryCommodityCode
     */
    protected $summaryCommodityCode = null;

    /**
     * @var string $supplierOrderReference
     */
    protected $supplierOrderReference = null;

    /**
     * @var string $userPO
     */
    protected $userPO = null;

    /**
     * @var string $costCenter
     */
    protected $costCenter = null;

    /**
     * @var string $purchaserCode
     */
    protected $purchaserCode = null;

    /**
     * @var boolean $taxable
     */
    protected $taxable = null;

    /**
     * @var string $amexDataTAA1
     */
    protected $amexDataTAA1 = null;

    /**
     * @var string $amexDataTAA2
     */
    protected $amexDataTAA2 = null;

    /**
     * @var string $amexDataTAA3
     */
    protected $amexDataTAA3 = null;

    /**
     * @var string $amexDataTAA4
     */
    protected $amexDataTAA4 = null;

    /**
     * @var string $invoiceDate
     */
    protected $invoiceDate = null;

    /**
     * @var string $totalTaxTypeCode
     */
    protected $totalTaxTypeCode = null;

    /**
     * @var string $cardAcceptorRefNumber
     */
    protected $cardAcceptorRefNumber = null;

    /**
     * @var string $authorizedContactName
     */
    protected $authorizedContactName = null;

    /**
     * @var string $businessApplicationID
     */
    protected $businessApplicationID = null;

    /**
     * @var int $salesOrganizationID
     */
    protected $salesOrganizationID = null;

    /**
     * @var string $submerchantID
     */
    protected $submerchantID = null;

    /**
     * @var string $submerchantName
     */
    protected $submerchantName = null;

    /**
     * @var string $submerchantStreet
     */
    protected $submerchantStreet = null;

    /**
     * @var string $submerchantCity
     */
    protected $submerchantCity = null;

    /**
     * @var string $submerchantPostalCode
     */
    protected $submerchantPostalCode = null;

    /**
     * @var string $submerchantState
     */
    protected $submerchantState = null;

    /**
     * @var string $submerchantCountry
     */
    protected $submerchantCountry = null;

    /**
     * @var string $submerchantEmail
     */
    protected $submerchantEmail = null;

    /**
     * @var string $submerchantTelephoneNumber
     */
    protected $submerchantTelephoneNumber = null;

    /**
     * @var string $submerchantRegion
     */
    protected $submerchantRegion = null;

    /**
     * @var string $submerchantMerchantID
     */
    protected $submerchantMerchantID = null;

    /**
     * @var string $merchantDescriptorCounty
     */
    protected $merchantDescriptorCounty = null;

    /**
     * @var string $referenceDataCode
     */
    protected $referenceDataCode = null;

    /**
     * @var string $referenceDataNumber
     */
    protected $referenceDataNumber = null;

    /**
     * @var string $merchantDescriptorStoreID
     */
    protected $merchantDescriptorStoreID = null;

    /**
     * @var string $clerkID
     */
    protected $clerkID = null;

    /**
     * @var string $customData_1
     */
    protected $customData_1 = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return string
     */
    public function getMerchantDescriptor()
    {
      return $this->merchantDescriptor;
    }

    /**
     * @param string $merchantDescriptor
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptor($merchantDescriptor)
    {
      $this->merchantDescriptor = $merchantDescriptor;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorContact()
    {
      return $this->merchantDescriptorContact;
    }

    /**
     * @param string $merchantDescriptorContact
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorContact($merchantDescriptorContact)
    {
      $this->merchantDescriptorContact = $merchantDescriptorContact;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorAlternate()
    {
      return $this->merchantDescriptorAlternate;
    }

    /**
     * @param string $merchantDescriptorAlternate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorAlternate($merchantDescriptorAlternate)
    {
      $this->merchantDescriptorAlternate = $merchantDescriptorAlternate;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorStreet()
    {
      return $this->merchantDescriptorStreet;
    }

    /**
     * @param string $merchantDescriptorStreet
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorStreet($merchantDescriptorStreet)
    {
      $this->merchantDescriptorStreet = $merchantDescriptorStreet;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorCity()
    {
      return $this->merchantDescriptorCity;
    }

    /**
     * @param string $merchantDescriptorCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorCity($merchantDescriptorCity)
    {
      $this->merchantDescriptorCity = $merchantDescriptorCity;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorState()
    {
      return $this->merchantDescriptorState;
    }

    /**
     * @param string $merchantDescriptorState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorState($merchantDescriptorState)
    {
      $this->merchantDescriptorState = $merchantDescriptorState;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorPostalCode()
    {
      return $this->merchantDescriptorPostalCode;
    }

    /**
     * @param string $merchantDescriptorPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorPostalCode($merchantDescriptorPostalCode)
    {
      $this->merchantDescriptorPostalCode = $merchantDescriptorPostalCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorCountry()
    {
      return $this->merchantDescriptorCountry;
    }

    /**
     * @param string $merchantDescriptorCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorCountry($merchantDescriptorCountry)
    {
      $this->merchantDescriptorCountry = $merchantDescriptorCountry;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsGift()
    {
      return $this->isGift;
    }

    /**
     * @param boolean $isGift
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setIsGift($isGift)
    {
      $this->isGift = $isGift;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReturnsAccepted()
    {
      return $this->returnsAccepted;
    }

    /**
     * @param boolean $returnsAccepted
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setReturnsAccepted($returnsAccepted)
    {
      $this->returnsAccepted = $returnsAccepted;
      return $this;
    }

    /**
     * @return string
     */
    public function getTenderType()
    {
      return $this->tenderType;
    }

    /**
     * @param string $tenderType
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setTenderType($tenderType)
    {
      $this->tenderType = $tenderType;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantVATRegistrationNumber()
    {
      return $this->merchantVATRegistrationNumber;
    }

    /**
     * @param string $merchantVATRegistrationNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantVATRegistrationNumber($merchantVATRegistrationNumber)
    {
      $this->merchantVATRegistrationNumber = $merchantVATRegistrationNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getPurchaserOrderDate()
    {
      return $this->purchaserOrderDate;
    }

    /**
     * @param string $purchaserOrderDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setPurchaserOrderDate($purchaserOrderDate)
    {
      $this->purchaserOrderDate = $purchaserOrderDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getPurchaserVATRegistrationNumber()
    {
      return $this->purchaserVATRegistrationNumber;
    }

    /**
     * @param string $purchaserVATRegistrationNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setPurchaserVATRegistrationNumber($purchaserVATRegistrationNumber)
    {
      $this->purchaserVATRegistrationNumber = $purchaserVATRegistrationNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getVatInvoiceReferenceNumber()
    {
      return $this->vatInvoiceReferenceNumber;
    }

    /**
     * @param string $vatInvoiceReferenceNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setVatInvoiceReferenceNumber($vatInvoiceReferenceNumber)
    {
      $this->vatInvoiceReferenceNumber = $vatInvoiceReferenceNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getSummaryCommodityCode()
    {
      return $this->summaryCommodityCode;
    }

    /**
     * @param string $summaryCommodityCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSummaryCommodityCode($summaryCommodityCode)
    {
      $this->summaryCommodityCode = $summaryCommodityCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getSupplierOrderReference()
    {
      return $this->supplierOrderReference;
    }

    /**
     * @param string $supplierOrderReference
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSupplierOrderReference($supplierOrderReference)
    {
      $this->supplierOrderReference = $supplierOrderReference;
      return $this;
    }

    /**
     * @return string
     */
    public function getUserPO()
    {
      return $this->userPO;
    }

    /**
     * @param string $userPO
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setUserPO($userPO)
    {
      $this->userPO = $userPO;
      return $this;
    }

    /**
     * @return string
     */
    public function getCostCenter()
    {
      return $this->costCenter;
    }

    /**
     * @param string $costCenter
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setCostCenter($costCenter)
    {
      $this->costCenter = $costCenter;
      return $this;
    }

    /**
     * @return string
     */
    public function getPurchaserCode()
    {
      return $this->purchaserCode;
    }

    /**
     * @param string $purchaserCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setPurchaserCode($purchaserCode)
    {
      $this->purchaserCode = $purchaserCode;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getTaxable()
    {
      return $this->taxable;
    }

    /**
     * @param boolean $taxable
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setTaxable($taxable)
    {
      $this->taxable = $taxable;
      return $this;
    }

    /**
     * @return string
     */
    public function getAmexDataTAA1()
    {
      return $this->amexDataTAA1;
    }

    /**
     * @param string $amexDataTAA1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setAmexDataTAA1($amexDataTAA1)
    {
      $this->amexDataTAA1 = $amexDataTAA1;
      return $this;
    }

    /**
     * @return string
     */
    public function getAmexDataTAA2()
    {
      return $this->amexDataTAA2;
    }

    /**
     * @param string $amexDataTAA2
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setAmexDataTAA2($amexDataTAA2)
    {
      $this->amexDataTAA2 = $amexDataTAA2;
      return $this;
    }

    /**
     * @return string
     */
    public function getAmexDataTAA3()
    {
      return $this->amexDataTAA3;
    }

    /**
     * @param string $amexDataTAA3
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setAmexDataTAA3($amexDataTAA3)
    {
      $this->amexDataTAA3 = $amexDataTAA3;
      return $this;
    }

    /**
     * @return string
     */
    public function getAmexDataTAA4()
    {
      return $this->amexDataTAA4;
    }

    /**
     * @param string $amexDataTAA4
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setAmexDataTAA4($amexDataTAA4)
    {
      $this->amexDataTAA4 = $amexDataTAA4;
      return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceDate()
    {
      return $this->invoiceDate;
    }

    /**
     * @param string $invoiceDate
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setInvoiceDate($invoiceDate)
    {
      $this->invoiceDate = $invoiceDate;
      return $this;
    }

    /**
     * @return string
     */
    public function getTotalTaxTypeCode()
    {
      return $this->totalTaxTypeCode;
    }

    /**
     * @param string $totalTaxTypeCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setTotalTaxTypeCode($totalTaxTypeCode)
    {
      $this->totalTaxTypeCode = $totalTaxTypeCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getCardAcceptorRefNumber()
    {
      return $this->cardAcceptorRefNumber;
    }

    /**
     * @param string $cardAcceptorRefNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setCardAcceptorRefNumber($cardAcceptorRefNumber)
    {
      $this->cardAcceptorRefNumber = $cardAcceptorRefNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizedContactName()
    {
      return $this->authorizedContactName;
    }

    /**
     * @param string $authorizedContactName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setAuthorizedContactName($authorizedContactName)
    {
      $this->authorizedContactName = $authorizedContactName;
      return $this;
    }

    /**
     * @return string
     */
    public function getBusinessApplicationID()
    {
      return $this->businessApplicationID;
    }

    /**
     * @param string $businessApplicationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setBusinessApplicationID($businessApplicationID)
    {
      $this->businessApplicationID = $businessApplicationID;
      return $this;
    }

    /**
     * @return int
     */
    public function getSalesOrganizationID()
    {
      return $this->salesOrganizationID;
    }

    /**
     * @param int $salesOrganizationID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSalesOrganizationID($salesOrganizationID)
    {
      $this->salesOrganizationID = $salesOrganizationID;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantID()
    {
      return $this->submerchantID;
    }

    /**
     * @param string $submerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantID($submerchantID)
    {
      $this->submerchantID = $submerchantID;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantName()
    {
      return $this->submerchantName;
    }

    /**
     * @param string $submerchantName
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantName($submerchantName)
    {
      $this->submerchantName = $submerchantName;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantStreet()
    {
      return $this->submerchantStreet;
    }

    /**
     * @param string $submerchantStreet
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantStreet($submerchantStreet)
    {
      $this->submerchantStreet = $submerchantStreet;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantCity()
    {
      return $this->submerchantCity;
    }

    /**
     * @param string $submerchantCity
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantCity($submerchantCity)
    {
      $this->submerchantCity = $submerchantCity;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantPostalCode()
    {
      return $this->submerchantPostalCode;
    }

    /**
     * @param string $submerchantPostalCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantPostalCode($submerchantPostalCode)
    {
      $this->submerchantPostalCode = $submerchantPostalCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantState()
    {
      return $this->submerchantState;
    }

    /**
     * @param string $submerchantState
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantState($submerchantState)
    {
      $this->submerchantState = $submerchantState;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantCountry()
    {
      return $this->submerchantCountry;
    }

    /**
     * @param string $submerchantCountry
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantCountry($submerchantCountry)
    {
      $this->submerchantCountry = $submerchantCountry;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantEmail()
    {
      return $this->submerchantEmail;
    }

    /**
     * @param string $submerchantEmail
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantEmail($submerchantEmail)
    {
      $this->submerchantEmail = $submerchantEmail;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantTelephoneNumber()
    {
      return $this->submerchantTelephoneNumber;
    }

    /**
     * @param string $submerchantTelephoneNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantTelephoneNumber($submerchantTelephoneNumber)
    {
      $this->submerchantTelephoneNumber = $submerchantTelephoneNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantRegion()
    {
      return $this->submerchantRegion;
    }

    /**
     * @param string $submerchantRegion
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantRegion($submerchantRegion)
    {
      $this->submerchantRegion = $submerchantRegion;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubmerchantMerchantID()
    {
      return $this->submerchantMerchantID;
    }

    /**
     * @param string $submerchantMerchantID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setSubmerchantMerchantID($submerchantMerchantID)
    {
      $this->submerchantMerchantID = $submerchantMerchantID;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorCounty()
    {
      return $this->merchantDescriptorCounty;
    }

    /**
     * @param string $merchantDescriptorCounty
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorCounty($merchantDescriptorCounty)
    {
      $this->merchantDescriptorCounty = $merchantDescriptorCounty;
      return $this;
    }

    /**
     * @return string
     */
    public function getReferenceDataCode()
    {
      return $this->referenceDataCode;
    }

    /**
     * @param string $referenceDataCode
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setReferenceDataCode($referenceDataCode)
    {
      $this->referenceDataCode = $referenceDataCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getReferenceDataNumber()
    {
      return $this->referenceDataNumber;
    }

    /**
     * @param string $referenceDataNumber
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setReferenceDataNumber($referenceDataNumber)
    {
      $this->referenceDataNumber = $referenceDataNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getMerchantDescriptorStoreID()
    {
      return $this->merchantDescriptorStoreID;
    }

    /**
     * @param string $merchantDescriptorStoreID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setMerchantDescriptorStoreID($merchantDescriptorStoreID)
    {
      $this->merchantDescriptorStoreID = $merchantDescriptorStoreID;
      return $this;
    }

    /**
     * @return string
     */
    public function getClerkID()
    {
      return $this->clerkID;
    }

    /**
     * @param string $clerkID
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setClerkID($clerkID)
    {
      $this->clerkID = $clerkID;
      return $this;
    }

    /**
     * @return string
     */
    public function getCustomData_1()
    {
      return $this->customData_1;
    }

    /**
     * @param string $customData_1
     * @return \ParadoxLabs\CyberSource\Gateway\Api\InvoiceHeader
     */
    public function setCustomData_1($customData_1)
    {
      $this->customData_1 = $customData_1;
      return $this;
    }

}
