<?php declare(strict_types=1);
/**
 * Copyright © 2015-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Test\Unit\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote\Payment;
use ParadoxLabs\CyberSource\Observer\PaymentMethodAssignDataObserver;
use ParadoxLabs\TokenBase\Api\CardRepositoryInterface;
use ParadoxLabs\TokenBase\Api\Data\CardInterface;
use ParadoxLabs\TokenBase\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unified Checkout assignData seam: the observer must copy transient_token from the client
 * additional_data contract into payment additional_information, without disturbing the
 * TokenBase-handled keys (card_id, save, cc_*) and without copying unknown keys.
 */
class PaymentMethodAssignDataObserverTest extends TestCase
{
    private PaymentMethodAssignDataObserver $observer;
    private Data|MockObject $helperMock;
    private CardRepositoryInterface|MockObject $cardRepositoryMock;
    private MethodInterface|MockObject $methodMock;
    private Payment|MockObject $paymentMock;

    /**
     * @var array<string, mixed>
     */
    private array $additionalInformation = [];

    protected function setUp(): void
    {
        $this->helperMock = $this->createMock(Data::class);
        $this->cardRepositoryMock = $this->createMock(CardRepositoryInterface::class);

        $this->observer = new PaymentMethodAssignDataObserver(
            $this->helperMock,
            $this->cardRepositoryMock,
        );

        $this->methodMock = $this->createMock(MethodInterface::class);

        $this->additionalInformation = [];

        $this->paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getAdditionalInformation',
                'setAdditionalInformation',
                'getMethod',
                'getExtensionAttributes',
            ])
            ->getMock();
        $this->paymentMock->method('getMethod')
            ->willReturn('paradoxlabs_cybersource');
        $this->paymentMock->method('getExtensionAttributes')
            ->willReturn(null);
        $this->paymentMock->method('setAdditionalInformation')
            ->willReturnCallback(function ($key, $value = null) {
                $this->additionalInformation[$key] = $value;

                return $this->paymentMock;
            });
        $this->paymentMock->method('getAdditionalInformation')
            ->willReturnCallback(function ($key = null) {
                if ($key === null) {
                    return $this->additionalInformation;
                }

                return $this->additionalInformation[$key] ?? null;
            });
    }

    private function createEventObserver(DataObject $data): Observer
    {
        $observer = new Observer();
        $observer->setData('method', $this->methodMock);
        $observer->setData('payment_model', $this->paymentMock);
        $observer->setData('data', $data);

        return $observer;
    }

    public function testExecuteCopiesTransientTokenFromAdditionalData(): void
    {
        $data = new DataObject([
            'method' => 'paradoxlabs_cybersource',
            'additional_data' => [
                'transient_token' => 'eyJraWQiOiJ0ZXN0In0.payload.sig',
                'card_id' => null,
                'cc_cid' => '123',
                'save' => true,
            ],
        ]);

        $this->observer->execute($this->createEventObserver($data));

        $this->assertSame(
            'eyJraWQiOiJ0ZXN0In0.payload.sig',
            $this->additionalInformation['transient_token'] ?? null
        );
    }

    public function testExecuteCopiesTopLevelTransientToken(): void
    {
        // Legacy/admin form path: fields arrive at the top level of $data, not under additional_data.
        $data = new DataObject([
            'method' => 'paradoxlabs_cybersource',
            'transient_token' => 'eyJraWQiOiJ0ZXN0In0.payload.sig',
        ]);

        $this->observer->execute($this->createEventObserver($data));

        $this->assertSame(
            'eyJraWQiOiJ0ZXN0In0.payload.sig',
            $this->additionalInformation['transient_token'] ?? null
        );
    }

    public function testExecuteDoesNotSetEmptyTransientToken(): void
    {
        $data = new DataObject([
            'method' => 'paradoxlabs_cybersource',
            'additional_data' => [
                'transient_token' => '',
            ],
        ]);

        $this->observer->execute($this->createEventObserver($data));

        $this->assertArrayNotHasKey('transient_token', $this->additionalInformation);
    }

    public function testExecuteIgnoresUnknownAdditionalDataKeys(): void
    {
        $data = new DataObject([
            'method' => 'paradoxlabs_cybersource',
            'additional_data' => [
                'transient_token' => 'eyJraWQiOiJ0ZXN0In0.payload.sig',
                'arbitrary_key' => 'injected-value',
            ],
        ]);

        $this->observer->execute($this->createEventObserver($data));

        $this->assertArrayNotHasKey('arbitrary_key', $this->additionalInformation);
    }

    public function testExecutePreservesTokenbaseStoredCardHandling(): void
    {
        // Stored-card path: no transient_token; TokenBase loads the card and records 'save'.
        $this->helperMock->method('getIsFrontend')
            ->willReturn(true);

        $cardMock = $this->createMock(CardInterface::class);
        $cardMock->method('getId')
            ->willReturn(42);
        $cardMock->method('getHash')
            ->willReturn('cardhash123');
        $cardMock->method('getAdditional')
            ->willReturn(null);

        $this->cardRepositoryMock->expects($this->once())
            ->method('getById')
            ->with('cardhash123')
            ->willReturn($cardMock);

        $data = new DataObject([
            'method' => 'paradoxlabs_cybersource',
            'additional_data' => [
                'transient_token' => null,
                'card_id' => 'cardhash123',
                'save' => 1,
            ],
        ]);

        $this->observer->execute($this->createEventObserver($data));

        $this->assertArrayNotHasKey('transient_token', $this->additionalInformation);
        $this->assertSame(1, $this->additionalInformation['save'] ?? null);
        $this->assertSame(42, $this->paymentMock->getData('tokenbase_id'));
    }
}
