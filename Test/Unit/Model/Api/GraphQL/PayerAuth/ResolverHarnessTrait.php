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

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Api\GraphQL\PayerAuth;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Model\Quote;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ManagementFactory;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use PHPUnit\Framework\MockObject\MockObject;
use ParadoxLabs\CyberSource\Helper\Data as CyberSourceHelper;

/**
 * Shared mock harness for the Payer Authentication GraphQL resolver tests.
 */
trait ResolverHarnessTrait
{
    private const CART_ID = 'maskedcart123';
    private const STORE_ID = 3;

    /**
     * @var GraphQL|MockObject
     */
    private $graphQLMock;

    /**
     * @var Management|MockObject
     */
    private $managementMock;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var CyberSourceHelper|MockObject
     */
    private $loggerMock;

    /**
     * @var ManagementFactory|MockObject
     */
    private $managementFactoryMock;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    /**
     * @var ContextInterface|MockObject
     */
    private $contextMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private $infoMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * Build the mock set every resolver test shares. Defaults: active method, resolvable cart.
     *
     * @return void
     */
    private function setUpHarness(): void
    {
        $this->quoteMock = $this->createMock(Quote::class);
        $this->quoteMock->method('getStoreId')->willReturn(self::STORE_ID);

        $this->graphQLMock = $this->createMock(GraphQL::class);
        $this->graphQLMock->method('getQuote')->willReturn($this->quoteMock);

        $this->managementMock = $this->createMock(Management::class);
        $this->managementMock->method('setQuote')->willReturnSelf();

        $this->managementFactoryMock = $this->createMock(ManagementFactory::class);
        $this->managementFactoryMock->method('create')->willReturn($this->managementMock);

        $this->configMock = $this->createMock(Config::class);
        $this->configMock->method('moduleIsActive')->willReturn(true);

        $this->loggerMock  = $this->createMock(CyberSourceHelper::class);
        $this->fieldMock   = $this->createMock(Field::class);
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->infoMock    = $this->createMock(ResolveInfo::class);
    }

    /**
     * Invoke a resolver with a schema-shaped input array.
     *
     * @param \Magento\Framework\GraphQl\Query\ResolverInterface $resolver
     * @param array<string, mixed>|null $input
     * @return array<string, mixed>
     */
    private function resolveWith($resolver, ?array $input): array
    {
        return $resolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            null,
            $input === null ? [] : ['input' => $input]
        );
    }
}
