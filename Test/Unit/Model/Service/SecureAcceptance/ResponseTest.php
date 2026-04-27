<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\SecureAcceptance;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\SecurityViolationException;
use ParadoxLabs\CyberSource\Helper\Data;
use ParadoxLabs\CyberSource\Model\Card;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Hmac;
use ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Response;
use ParadoxLabs\CyberSource\Model\Source\CardType;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterfaceFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Service\SecureAcceptance\Response
 */
class ResponseTest extends TestCase
{
    private Response $response;
    private Hmac|MockObject $hmacMock;
    private CardRepositoryInterface|MockObject $cardRepositoryMock;
    private CardInterfaceFactory|MockObject $cardFactoryMock;
    private Data|MockObject $helperMock;
    private CardType|MockObject $cardTypeMock;
    private Address|MockObject $addressHelperMock;

    protected function setUp(): void
    {
        $this->hmacMock = $this->createMock(Hmac::class);
        $this->cardRepositoryMock = $this->createMock(CardRepositoryInterface::class);
        $this->cardFactoryMock = $this->createMock(CardInterfaceFactory::class);
        $this->helperMock = $this->createMock(Data::class);
        $this->cardTypeMock = $this->createMock(CardType::class);
        $this->addressHelperMock = $this->createMock(Address::class);

        $this->response = new Response(
            $this->hmacMock,
            $this->cardRepositoryMock,
            $this->cardFactoryMock,
            $this->helperMock,
            $this->cardTypeMock,
            $this->addressHelperMock,
        );
    }

    public function testValidateRequestInvalidSignatureThrowsSecurityException(): void
    {
        $input = [
            'signature_validation' => false,
            'decision' => 'ACCEPT',
        ];

        $this->expectException(SecurityViolationException::class);
        $this->expectExceptionMessage('Invalid request signature');

        $this->invokeValidateRequest($input);
    }

    public function testValidateRequestDeclineThrowsInputException(): void
    {
        $input = [
            'signature_validation' => true,
            'decision' => 'DECLINE',
            'reason_code' => '201',
            'message' => 'Card declined',
        ];

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Credit card was not accepted');

        $this->invokeValidateRequest($input);
    }

    public function testValidateRequestErrorThrowsInputException(): void
    {
        $input = [
            'signature_validation' => true,
            'decision' => 'ERROR',
            'reason_code' => '150',
            'message' => 'System error',
        ];

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('An error occurred');

        $this->invokeValidateRequest($input);
    }

    public function testValidateRequestCancelThrowsInputException(): void
    {
        $input = [
            'signature_validation' => true,
            'decision' => 'CANCEL',
            'reason_code' => '100',
        ];

        $this->expectException(InputException::class);

        $this->invokeValidateRequest($input);
    }

    public function testValidateRequestReviewThrowsInputException(): void
    {
        $input = [
            'signature_validation' => true,
            'decision' => 'REVIEW',
            'reason_code' => '480',
        ];

        $this->expectException(InputException::class);

        $this->invokeValidateRequest($input);
    }

    public function testValidateRequestAcceptPasses(): void
    {
        $input = [
            'signature_validation' => true,
            'decision' => 'ACCEPT',
        ];

        // Should not throw
        $this->invokeValidateRequest($input);
        $this->assertTrue(true);
    }

    public function testValidateRequestIncludesInvalidFields(): void
    {
        $input = [
            'signature_validation' => true,
            'decision' => 'DECLINE',
            'reason_code' => '102',
            'message' => 'Invalid data',
            'invalid_fields' => 'card_number,card_expiry',
        ];

        try {
            $this->invokeValidateRequest($input);
            $this->fail('Expected InputException');
        } catch (InputException $e) {
            $this->assertStringContainsString('card_number,card_expiry', $e->getMessage());
        }
    }

    public function testSetCardPaymentInfoExpiryParsing(): void
    {
        $cardMock = $this->createMock(Card::class);

        // Test MM/YYYY format -> YYYY-MM-DD 23:59:59
        $input = ['req_card_expiry_date' => '12/2025', 'req_card_number' => '4111111111111111', 'req_card_type' => '001'];

        $cardMock->expects($this->once())
            ->method('setExpires')
            ->with($this->callback(function ($value) {
                // Should be 2025-12-31 23:59:59
                return str_starts_with($value, '2025-12-') && str_ends_with($value, '23:59:59');
            }));

        $cardMock->method('setAdditional')->willReturnSelf();
        $this->cardTypeMock->method('getType')->willReturn('VI');

        $this->invokeSetCardPaymentInfo($input, $cardMock);
    }

    public function testSetCardPaymentInfoLast4Normal(): void
    {
        $cardMock = $this->createMock(Card::class);

        $input = [
            'req_card_expiry_date' => '06/2024',
            'req_card_number' => '4111111111111111',
            'req_card_type' => '001',
        ];

        $expectedCalls = [];
        $cardMock->method('setAdditional')
            ->willReturnCallback(function ($key, $value) use (&$expectedCalls, $cardMock) {
                $expectedCalls[$key] = $value;
                return $cardMock;
            });
        $cardMock->method('setExpires')->willReturnSelf();
        $this->cardTypeMock->method('getType')->willReturn('VI');

        $this->invokeSetCardPaymentInfo($input, $cardMock);

        $this->assertSame('1111', $expectedCalls['cc_last4']);
    }

    public function testSetCardPaymentInfoLast4Masked(): void
    {
        $cardMock = $this->createMock(Card::class);

        $input = [
            'req_card_expiry_date' => '06/2024',
            'req_card_number' => 'xxxxxxxxxxxx1234',
            'req_card_type' => '001',
        ];

        $expectedCalls = [];
        $cardMock->method('setAdditional')
            ->willReturnCallback(function ($key, $value) use (&$expectedCalls, $cardMock) {
                $expectedCalls[$key] = $value;
                return $cardMock;
            });
        $cardMock->method('setExpires')->willReturnSelf();
        $this->cardTypeMock->method('getType')->willReturn('VI');

        $this->invokeSetCardPaymentInfo($input, $cardMock);

        // When masked (last4 is xxxx), should still work
        $this->assertSame('1234', $expectedCalls['cc_last4']);
    }

    public function testSetCardPaymentInfoLast4FromInstrumentId(): void
    {
        $cardMock = $this->createMock(Card::class);

        $input = [
            'req_card_expiry_date' => '06/2024',
            'req_card_number' => 'xxxxxxxxxxxx',
            'req_card_type' => '001',
            'payment_token_instrument_identifier_id' => 'INSTR5678',
        ];

        $expectedCalls = [];
        $cardMock->method('setAdditional')
            ->willReturnCallback(function ($key, $value) use (&$expectedCalls, $cardMock) {
                $expectedCalls[$key] = $value;
                return $cardMock;
            });
        $cardMock->method('setExpires')->willReturnSelf();
        $this->cardTypeMock->method('getType')->willReturn('VI');

        $this->invokeSetCardPaymentInfo($input, $cardMock);

        // When card number ends with xxxx, use instrument identifier
        $this->assertSame('5678', $expectedCalls['cc_last4']);
    }

    public function testSetCardPaymentInfoCardTypeMapping(): void
    {
        $cardMock = $this->createMock(Card::class);

        $input = [
            'req_card_expiry_date' => '06/2024',
            'req_card_number' => '4111111111111111',
            'req_card_type' => '002',
        ];

        $this->cardTypeMock->expects($this->once())
            ->method('getType')
            ->with('002')
            ->willReturn('MC');

        $expectedCalls = [];
        $cardMock->method('setAdditional')
            ->willReturnCallback(function ($key, $value) use (&$expectedCalls, $cardMock) {
                $expectedCalls[$key] = $value;
                return $cardMock;
            });
        $cardMock->method('setExpires')->willReturnSelf();

        $this->invokeSetCardPaymentInfo($input, $cardMock);

        $this->assertSame('MC', $expectedCalls['cc_type']);
    }

    public function testGetCardExistingCardLookup(): void
    {
        $existingCard = $this->createMock(Card::class);
        $existingCard->method('getMethod')->willReturn(Config::CODE);
        $existingCard->method('getCustomerId')->willReturn(123);

        $this->cardRepositoryMock->expects($this->once())
            ->method('getByHash')
            ->with('card-hash-123')
            ->willReturn($existingCard);

        $input = [
            'req_merchant_defined_data99' => 'card-hash-123',
            'req_consumer_id' => '123',
        ];

        $result = $this->invokeGetCard($input);

        $this->assertSame($existingCard, $result);
    }

    public function testGetCardNewCardCreation(): void
    {
        $newCard = $this->createMock(Card::class);
        $newCard->expects($this->once())->method('setMethod')->with(Config::CODE);
        $newCard->expects($this->once())->method('setActive')->with(0);

        $this->cardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newCard);

        $input = [
            'req_consumer_id' => '456',
        ];

        $result = $this->invokeGetCard($input);

        $this->assertSame($newCard, $result);
    }

    public function testGetCardNewCardActiveFromMerchantData100(): void
    {
        $newCard = $this->createMock(Card::class);
        $newCard->method('setMethod')->willReturnSelf();

        // Card is set active(0) first, then active(1) when merchant_defined_data100 is 'paymentinfo'
        $newCard->expects($this->exactly(2))
            ->method('setActive')
            ->willReturnCallback(function ($value) use ($newCard) {
                static $callCount = 0;
                $callCount++;
                if ($callCount === 1) {
                    $this->assertSame(0, $value);
                } else {
                    $this->assertSame(1, $value);
                }
                return $newCard;
            });

        $this->cardFactoryMock->method('create')->willReturn($newCard);

        $input = [
            'req_consumer_id' => '456',
            'req_merchant_defined_data100' => 'paymentinfo',
        ];

        $this->invokeGetCard($input);
    }

    public function testGetCardCustomerIdMismatch(): void
    {
        $existingCard = $this->createMock(Card::class);
        $existingCard->method('getMethod')->willReturn(Config::CODE);
        $existingCard->method('getCustomerId')->willReturn(999); // Different customer

        $this->cardRepositoryMock->method('getByHash')->willReturn($existingCard);

        $newCard = $this->createMock(Card::class);
        $newCard->method('setMethod')->willReturnSelf();
        $newCard->method('setActive')->willReturnSelf();

        $this->cardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newCard);

        $input = [
            'req_merchant_defined_data99' => 'card-hash-123',
            'req_consumer_id' => '123', // Different from card's customer
        ];

        $result = $this->invokeGetCard($input);

        $this->assertSame($newCard, $result);
    }

    /**
     * Helper method to invoke protected validateRequest method
     */
    private function invokeValidateRequest(array $input): void
    {
        $method = new ReflectionMethod(Response::class, 'validateRequest');
        $method->setAccessible(true);
        $method->invoke($this->response, $input);
    }

    /**
     * Helper method to invoke protected setCardPaymentInfo method
     */
    private function invokeSetCardPaymentInfo(array $input, $card): void
    {
        $method = new ReflectionMethod(Response::class, 'setCardPaymentInfo');
        $method->setAccessible(true);
        $method->invoke($this->response, $input, $card);
    }

    /**
     * Helper method to invoke protected getCard method
     */
    private function invokeGetCard(array $input)
    {
        $method = new ReflectionMethod(Response::class, 'getCard');
        $method->setAccessible(true);
        return $method->invoke($this->response, $input);
    }
}
