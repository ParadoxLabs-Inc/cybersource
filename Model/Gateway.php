<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <support@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\PaymentException;
use ParadoxLabs\CyberSource\Gateway\Api;

/**
 * CyberSource API Gateway - custom built for perfection.
 */
class Gateway extends \ParadoxLabs\TokenBase\Model\AbstractGateway
{
    /**
     * @var string
     */
    protected $code = 'paradoxlabs_cybersource';

    /**
     * @var string
     */
    protected $endpointLive = 'https://secureacceptance.cybersource.com';

    /**
     * @var string
     */
    protected $endpointTest = 'https://testsecureacceptance.cybersource.com';

    /**
     * $fields defines validation for each API parameter or input.
     *
     * key => [
     *    'maxLength' => int,
     *    'noSymbols' => true|false,
     *    'charMask'  => (allowed characters in regex form),
     *    'enum'      => [ values ]
     * ]
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Initialize the gateway. Input is taken as an array for greater flexibility.
     *
     * @param array $parameters
     * @return $this
     */
    public function init(array $parameters)
    {
        // parent::init($parameters);

        // $soapOptions = [
        //     'encoding' => 'utf-8',
        //     'exceptions' => true,
        //     'connection_timeout' => 3,
        //     'cache_wsdl' => WSDL_CACHE_NONE,
        //     'trace' => 1,
        // ];
        // $merchantId = 'paradoxlabs';
        // $transactionKey = 'J5q4DNmc3kyrasfMPJB007YfH5tqOF931vZcXdp7gH/WL2imGszI67qXtdgOiWruYzu1dbb34+kvoMMEKYu2jm7LMrpOr0L/qugyFwFgG3C1nfZ+P95q2OByrh1Kk6TPtx9rv9fz4nnW3x9mrXbxX7+QXp+X0vq1g2DWtCmgMpLDOZOtbWUuhdqtuf/veFu/WujH5qxMjt9oZPztnr8yMnjBmZQ4tgM/7Z4kdmIhk0cIYlXdSqIrwhKEGP+GcIPU13vAeHXeIAUQZThDJemPvTdgKIkS9ySLGTHdMX9norJDdvggHHKukQu3s5T65FvmfmesqiBODBbrp1IXHO4vbg==';
        //
        // $request = new Api\RequestMessage();
        // $request->setMerchantReferenceCode('ABC123');
        //
        // $billTo = new Api\BillTo();
        // $billTo->setFirstName('John');
        // $billTo->setLastName('Doe');
        // $billTo->setStreet1('123 Test Ave.');
        // $billTo->setCity('Test City');
        // $billTo->setState('PA');
        // $billTo->setPostalCode('12345');
        // $billTo->setCountry('US');
        // $billTo->setPhoneNumber('555-555-5555');
        // $billTo->setEmail('test@example.com');
        // $billTo->setIpAddress('127.0.0.1');
        // $request->setBillTo($billTo);
        //
        // $shipTo = new Api\ShipTo();
        // $shipTo->setFirstName('John');
        // $shipTo->setLastName('Doe');
        // $shipTo->setStreet1('123 Test Ave.');
        // $shipTo->setCity('Test City');
        // $shipTo->setState('PA');
        // $shipTo->setPostalCode('12345');
        // $shipTo->setCountry('US');
        // $shipTo->setPhoneNumber('555-555-5555');
        // $request->setShipTo($shipTo);
        //
        // $item0 = new Api\Item(0);
        // $item0->setUnitPrice(1.99);
        // $item0->setTaxAmount(0);
        // $item0->setQuantity(1);
        // $item0->setProductSKU('Q993');
        // $request->setItem([$item0]);
        //
        // $purchaseTotals = new Api\PurchaseTotals();
        // $purchaseTotals->setCurrency('USD');
        // $request->setPurchaseTotals($purchaseTotals);
        //
        // $card = new Api\Card();
        // $card->setAccountNumber('4111111111111111');
        // $card->setCardType('001');
        // $card->setExpirationMonth(12);
        // $card->setExpirationYear(2021);
        // $request->setCard($card);
        //
        // $ccAuthService = new Api\CCAuthService('true');
        // $ccAuthService->setCommerceIndicator('moto');
        // $request->setCcAuthService($ccAuthService);
        //
        // $request->setClientLibrary('PHP');
        // $request->setClientLibraryVersion('7.2');
        //
        // $decisionManager = new Api\DecisionManager();
        // $decisionManager->setEnabled('false');
        // $request->setDecisionManager($decisionManager);
        //
        // try {
        //     $client = new Api\TransactionProcessor($soapOptions, 'https://ics2wstest.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.161.wsdl');
        //     $client->__setSoapHeaders(
        //         new Api\WsseHeader('', '', null, true, null, $merchantId, $transactionKey)
        //     );
        //     $result = $client->runTransaction($request);
        // } catch (\Exception $e) {
        // }

        return $this;
    }

    /**
     * Set the API credentials so they go through validation.
     *
     * @return $this
     * @throws PaymentException
     */
    public function clearParameters()
    {
        parent::clearParameters();

        if (isset($this->defaults['login'], $this->defaults['password'])) {
            $this->setParameter('apikey', $this->defaults['login']);
            $this->setParameter('token', $this->defaults['password']);
        }

        return $this;
    }

    /**
     * Run an auth transaction for $amount with the given payment info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        // TODO: Implement authorize() method.
    }

    /**
     * Run a capture transaction for $amount with the given payment info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount, $transactionId = null)
    {
        // TODO: Implement capture() method.
    }

    /**
     * Run a refund transaction for $amount with the given payment info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount, $transactionId = null)
    {
        // TODO: Implement refund() method.
    }

    /**
     * Run a void transaction for the given payment info
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment, $transactionId = null)
    {
        // TODO: Implement void() method.
    }

    /**
     * Fetch a transaction status update
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param string $transactionId
     * @return \ParadoxLabs\TokenBase\Model\Gateway\Response
     */
    public function fraudUpdate(\Magento\Payment\Model\InfoInterface $payment, $transactionId)
    {
        // TODO: Implement fraudUpdate() method.
    }
}
