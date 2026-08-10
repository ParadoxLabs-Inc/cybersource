<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use ParadoxLabs\CyberSource\Setup\Patch\Data\PurgeLegacyConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ParadoxLabs\CyberSource\Setup\Patch\Data\PurgeLegacyConfig
 */
class PurgeLegacyConfigTest extends TestCase
{
    public function testPurgesEveryRemovedLegacyPathInEveryScope(): void
    {
        $connection = $this->createMock(AdapterInterface::class);
        $connection->method('quoteInto')->willReturnCallback(
            static fn(string $text, $value): string => str_replace('?', "'" . implode("','", (array)$value) . "'", $text)
        );

        $captured = null;
        $connection->expects($this->once())
            ->method('delete')
            ->willReturnCallback(function (string $table, $where) use (&$captured): int {
                $this->assertSame('core_config_data', $table);
                $captured = $where;

                return 0;
            });

        $configResource = $this->createMock(Config::class);
        $configResource->method('getConnection')->willReturn($connection);
        $configResource->method('getTable')->willReturnArgument(0);

        (new PurgeLegacyConfig($this->createMock(ModuleDataSetupInterface::class), $configResource))->apply();

        $where = implode(' ', (array)$captured);

        foreach (PurgeLegacyConfig::getRemovedPaths() as $path) {
            $this->assertStringContainsString($path, $where);
        }

        // No scope predicate: the delete is by path alone, so website and store rows go too.
        $this->assertStringNotContainsString('scope', $where);
    }

    /**
     * Deleting these would silently disable 3D Secure for every merchant who had it turned on: both
     * are still live settings in 4.0.0, Payer Auth having moved onto the CyberSource account itself.
     */
    public function testLivePayerAuthSettingsAreNeverPurged(): void
    {
        $paths = PurgeLegacyConfig::getRemovedPaths();

        $this->assertNotContains('payment/paradoxlabs_cybersource/cardinal_active', $paths);
        $this->assertNotContains('payment/paradoxlabs_cybersource/cardinal_card_types', $paths);
    }

    public function testRemovedPathsCoverEveryDeadIntegration(): void
    {
        $paths = PurgeLegacyConfig::getRemovedPaths();

        $this->assertSame($paths, array_unique($paths), 'The path list must not repeat itself.');
        $this->assertCount(14, $paths);

        foreach ($paths as $path) {
            $this->assertStringStartsWith('payment/paradoxlabs_cybersource/', $path);
        }
    }
}
