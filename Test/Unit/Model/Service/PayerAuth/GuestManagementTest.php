<?php declare(strict_types=1);
/**
 * Copyright © 2020-present ParadoxLabs, Inc.
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

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Quote\Model\Quote;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthResultInterface;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Data\Result;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\GuestManagement;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ManagementFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see \ParadoxLabs\CyberSource\Model\Service\PayerAuth\GuestManagement
 */
class GuestManagementTest extends TestCase
{
    /**
     * @var Management|MockObject
     */
    private $management;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface|MockObject
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var CartRepositoryInterface|MockObject
     */
    private $cartRepository;

    /**
     * @var GuestManagement
     */
    private $guestManagement;

    protected function setUp(): void
    {
        $this->management = $this->createMock(Management::class);
        $this->management->method('setQuote')->willReturnSelf();

        $factory = $this->createMock(ManagementFactory::class);
        $factory->method('create')->willReturn($this->management);

        $this->maskedQuoteIdToQuoteId = $this->createMock(MaskedQuoteIdToQuoteIdInterface::class);
        $this->cartRepository         = $this->createMock(CartRepositoryInterface::class);

        $this->guestManagement = new GuestManagement(
            $factory,
            $this->maskedQuoteIdToQuoteId,
            $this->cartRepository
        );
    }

    public function testFinalizeResolvesTheMaskedCartAndDelegates(): void
    {
        $quote = $this->guestQuote();
        $this->maskedQuoteIdToQuoteId->expects($this->once())->method('execute')->with('masked-id')->willReturn(99);
        $this->cartRepository->expects($this->once())->method('getActive')->with(99)->willReturn($quote);

        $this->management->expects($this->once())->method('setQuote')->with($quote)->willReturnSelf();
        $this->management->expects($this->once())
            ->method('finalize')
            ->willReturn((new Result())->setStatus(PayerAuthResultInterface::STATUS_SUCCESS));

        $this->assertSame(
            PayerAuthResultInterface::STATUS_SUCCESS,
            $this->guestManagement->finalize('masked-id')->getStatus()
        );
    }

    /**
     * A masked id pointing at a customer's cart must not open that account's quote to an
     * anonymous caller.
     *
     * @return void
     */
    public function testACustomerCartIsRefusedOnTheGuestRoute(): void
    {
        $this->maskedQuoteIdToQuoteId->method('execute')->willReturn(99);
        $this->cartRepository->method('getActive')->willReturn($this->guestQuote(42));

        $this->management->expects($this->never())->method('setup');
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('No active cart was found for Payer Authentication.');

        $this->guestManagement->setup('masked-id', 'the.transient.token');
    }

    public function testAnUnknownMaskedIdPropagates(): void
    {
        $this->maskedQuoteIdToQuoteId->method('execute')
            ->willThrowException(new NoSuchEntityException(__('No such entity.')));

        $this->expectException(NoSuchEntityException::class);

        $this->guestManagement->setup('masked-id', 'the.transient.token');
    }

    /**
     * Build a quote mock with the given customer id (0/null = guest).
     *
     * @param int|null $customerId
     * @return Quote|MockObject
     */
    private function guestQuote(?int $customerId = null)
    {
        $quote = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->addMethods(['getCustomerId'])
            ->getMock();
        $quote->method('getCustomerId')->willReturn($customerId);

        return $quote;
    }
}
