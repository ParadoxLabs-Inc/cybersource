<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\CardinalCruise;

use Magento\Sales\Model\Order\Item;
use Magento\Customer\Model\AddressRegistry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as NewsletterCollectionFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;
use ParadoxLabs\TokenBase\Model\ResourceModel\Card\CollectionFactory as CardCollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\CardinalCruise\EnrollmentParams
 */
class EnrollmentParamsTest extends TestCase
{
    private EnrollmentParams $enrollmentParams;
    private RequestInterface|MockObject $requestMock;
    private Data|MockObject $helperMock;
    private CardCollectionFactory|MockObject $cardCollectionFactoryMock;
    private CollectionFactoryInterface|MockObject $orderCollectionFactoryMock;
    private NewsletterCollectionFactory|MockObject $newsletterCollectionFactoryMock;
    private AddressRegistry|MockObject $addressRegistryMock;

    protected function setUp(): void
    {
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->helperMock = $this->createMock(Data::class);
        $this->cardCollectionFactoryMock = $this->createMock(CardCollectionFactory::class);
        $this->orderCollectionFactoryMock = $this->createMock(CollectionFactoryInterface::class);
        $this->newsletterCollectionFactoryMock = $this->createMock(NewsletterCollectionFactory::class);
        $this->addressRegistryMock = $this->createMock(AddressRegistry::class);

        $this->enrollmentParams = new EnrollmentParams(
            $this->requestMock,
            $this->helperMock,
            $this->cardCollectionFactoryMock,
            $this->orderCollectionFactoryMock,
            $this->newsletterCollectionFactoryMock,
            $this->addressRegistryMock,
        );
    }

    /**
     * @dataProvider isNewCustomerDataProvider
     */
    public function testGetIsNewCustomer(?int $customerId, string $expected): void
    {
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->method('getCustomerId')
            ->willReturn($customerId);

        $result = $this->invokeProtectedMethod('getIsNewCustomer', [$orderMock]);

        $this->assertSame($expected, $result);
    }

    public static function isNewCustomerDataProvider(): array
    {
        return [
            'guest (null customer id)' => [null, 'true'],
            'guest (zero customer id)' => [0, 'true'],
            'logged in customer' => [123, 'false'],
            'logged in customer id 1' => [1, 'false'],
        ];
    }

    public function testGetAuthIndicatorReturns02ForSubscription(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->method('getAdditionalInformation')
            ->with('is_subscription_generated')
            ->willReturn(true);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getPayment')
            ->willReturn($paymentMock);

        $result = $this->invokeProtectedMethod('getAuthIndicator', [$orderMock]);

        $this->assertSame('02', $result);
    }

    public function testGetAuthIndicatorReturns01ForNonSubscription(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->method('getAdditionalInformation')
            ->with('is_subscription_generated')
            ->willReturn(false);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getPayment')
            ->willReturn($paymentMock);

        $result = $this->invokeProtectedMethod('getAuthIndicator', [$orderMock]);

        $this->assertSame('01', $result);
    }

    public function testGetAuthIndicatorReturns01WhenSubscriptionInfoNull(): void
    {
        $paymentMock = $this->createMock(Payment::class);
        $paymentMock->method('getAdditionalInformation')
            ->with('is_subscription_generated')
            ->willReturn(null);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getPayment')
            ->willReturn($paymentMock);

        $result = $this->invokeProtectedMethod('getAuthIndicator', [$orderMock]);

        $this->assertSame('01', $result);
    }

    public function testGetCardAddedDateFormatsCorrectly(): void
    {
        $cardMock = $this->createMock(CardInterface::class);
        $cardMock->method('getCreatedAt')
            ->willReturn('2024-06-15 14:30:00');

        $result = $this->invokeProtectedMethod('getCardAddedDate', [$cardMock]);

        $this->assertSame('20240615', $result);
    }

    public function testGetCardAddedDateHandlesDifferentDateFormats(): void
    {
        $cardMock = $this->createMock(CardInterface::class);
        $cardMock->method('getCreatedAt')
            ->willReturn('2023-01-01');

        $result = $this->invokeProtectedMethod('getCardAddedDate', [$cardMock]);

        $this->assertSame('20230101', $result);
    }

    /**
     * @dataProvider orderItemsCountDataProvider
     */
    public function testGetOrderItemsCount(int $itemCount): void
    {
        $items = array_fill(0, $itemCount, $this->createMock(Item::class));

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getAllVisibleItems')
            ->willReturn($items);

        $result = $this->invokeProtectedMethod('getOrderItemsCount', [$orderMock]);

        $this->assertSame($itemCount, $result);
    }

    public static function orderItemsCountDataProvider(): array
    {
        return [
            'no items' => [0],
            'single item' => [1],
            'multiple items' => [5],
            'many items' => [20],
        ];
    }

    public function testGetShippingAddressAddDateReturnsNullForVirtualOrder(): void
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getIsVirtual')
            ->willReturn(true);

        $result = $this->invokeProtectedMethod('getShippingAddressAddDate', [$orderMock]);

        $this->assertNull($result);
    }

    public function testGetShippingAddressAddDateReturnsMinus1ForGuest(): void
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getIsVirtual')
            ->willReturn(false);
        $orderMock->method('getCustomerId')
            ->willReturn(0);

        $result = $this->invokeProtectedMethod('getShippingAddressAddDate', [$orderMock]);

        $this->assertSame('-1', $result);
    }

    public function testGetShippingAddressAddDateReturns0ForNewAddress(): void
    {
        $shippingAddressMock = $this->createMock(OrderAddressInterface::class);
        $shippingAddressMock->method('getCustomerAddressId')
            ->willReturn(null);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getIsVirtual')
            ->willReturn(false);
        $orderMock->method('getCustomerId')
            ->willReturn(123);
        $orderMock->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $result = $this->invokeProtectedMethod('getShippingAddressAddDate', [$orderMock]);

        $this->assertSame('0', $result);
    }

    public function testGetShippingAddressAddDateReturnsDateForStoredAddress(): void
    {
        $shippingAddressMock = $this->createMock(OrderAddressInterface::class);
        $shippingAddressMock->method('getCustomerAddressId')
            ->willReturn(456);

        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getIsVirtual')
            ->willReturn(false);
        $orderMock->method('getCustomerId')
            ->willReturn(123);
        $orderMock->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $customerAddressMock = $this->createMock(DataObject::class);
        $customerAddressMock->method('getData')
            ->with('created_at')
            ->willReturn('2024-03-20 10:00:00');

        $this->addressRegistryMock->method('retrieve')
            ->with(456)
            ->willReturn($customerAddressMock);

        $result = $this->invokeProtectedMethod('getShippingAddressAddDate', [$orderMock]);

        $this->assertSame('20240320', $result);
    }

    /**
     * Invoke a protected method on the EnrollmentParams instance
     *
     * @param string $methodName
     * @param array $params
     * @return mixed
     */
    private function invokeProtectedMethod(string $methodName, array $params): mixed
    {
        $method = new ReflectionMethod(EnrollmentParams::class, $methodName);
        $method->setAccessible(true);

        return $method->invoke($this->enrollmentParams, ...$params);
    }
}
