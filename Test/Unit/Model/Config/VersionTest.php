<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Config;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\Context;
use Magento\Framework\Module\Dir;
use Magento\Framework\Registry;
use ParadoxLabs\CyberSource\Model\Config\Version;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \ParadoxLabs\CyberSource\Model\Config\Version
 */
class VersionTest extends TestCase
{
    private const MODULE_DIR = '/var/www/app/code/ParadoxLabs/CyberSource';

    private Version $model;
    private Dir|MockObject $moduleDirMock;
    private File|MockObject $fileHandlerMock;

    protected function setUp(): void
    {
        $contextMock = $this->createMock(Context::class);
        $contextMock->method('getEventDispatcher')->willReturn($this->createMock(EventManager::class));

        $this->moduleDirMock = $this->createMock(Dir::class);
        $this->fileHandlerMock = $this->createMock(File::class);

        $this->model = new Version(
            $contextMock,
            $this->createMock(Registry::class),
            $this->createMock(ScopeConfigInterface::class),
            $this->createMock(TypeListInterface::class),
            $this->moduleDirMock,
            $this->fileHandlerMock,
            null,
            null,
            [],
        );
    }

    /**
     * Stub the composer.json read for the CyberSource module dir.
     */
    private function primeComposerJson(mixed $contents): void
    {
        $this->moduleDirMock->method('getDir')
            ->with('ParadoxLabs_CyberSource')
            ->willReturn(self::MODULE_DIR);

        $this->fileHandlerMock->method('read')
            ->with(self::MODULE_DIR . '/composer.json')
            ->willReturn($contents);
    }

    /**
     * Stub the composer.json read to throw.
     */
    private function primeComposerJsonThrows(\Throwable $exception): void
    {
        $this->moduleDirMock->method('getDir')->willReturn(self::MODULE_DIR);
        $this->fileHandlerMock->method('read')->willThrowException($exception);
    }

    private function invokeAfterLoad(): Version
    {
        $method = new ReflectionMethod(Version::class, '_afterLoad');
        $method->setAccessible(true);

        return $method->invoke($this->model);
    }

    public function testGetDefaultValueReturnsVersionAndReleaseDate(): void
    {
        $this->primeComposerJson(json_encode(['version' => '5.1.0', 'time' => '2026-07-01']));

        $this->assertSame('5.1.0 (2026-07-01)', $this->model->_getDefaultValue());
    }

    public function testGetDefaultValueReturnsVersionAloneWhenTimeAbsent(): void
    {
        $this->primeComposerJson(json_encode(['version' => '5.1.0']));

        $this->assertSame('5.1.0', $this->model->_getDefaultValue());
    }

    /**
     * A `time` key with no `version` is not a version; it must fall through to the unknown message
     * rather than rendering a bare date.
     */
    public function testGetDefaultValueFallsBackWhenVersionAbsentButTimePresent(): void
    {
        $this->primeComposerJson(json_encode(['time' => '2026-07-01']));

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    public function testGetDefaultValueFallsBackWhenComposerJsonHasNoVersionKeys(): void
    {
        $this->primeComposerJson(json_encode(['name' => 'paradoxlabs/cybersource']));

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    /**
     * Corrupt composer.json must degrade to the unknown message, never surface a JSON error into
     * the admin config form.
     */
    public function testGetDefaultValueFallsBackOnMalformedJson(): void
    {
        $this->primeComposerJson('{not valid json');

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    public function testGetDefaultValueFallsBackWhenFileIsEmpty(): void
    {
        $this->primeComposerJson('');

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    /**
     * File::read() returns false on failure rather than throwing; that must degrade to the unknown
     * message too.
     */
    public function testGetDefaultValueFallsBackWhenReadReturnsFalse(): void
    {
        $this->primeComposerJson(false);

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    /**
     * A read failure (missing file, permissions) must be swallowed: the version display is
     * cosmetic and must never break the admin config page.
     */
    public function testGetDefaultValueFallsBackWhenReadThrows(): void
    {
        $this->primeComposerJsonThrows(new FileSystemException(__('Cannot read file')));

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    /**
     * The catch is on Throwable, so even an Error from the filesystem layer must degrade cleanly.
     */
    public function testGetDefaultValueFallsBackWhenReadThrowsError(): void
    {
        $this->primeComposerJsonThrows(new \Error('boom'));

        $this->assertStringContainsString('Unknown', (string)$this->model->_getDefaultValue());
    }

    /**
     * The declared return type is string; the config form and processValue() both consume it as
     * one. Returning a Phrase on the fallback path breaks that contract.
     */
    public function testGetDefaultValueAlwaysReturnsAString(): void
    {
        $this->primeComposerJson('{not valid json');

        $this->assertIsString($this->model->_getDefaultValue());
    }

    /**
     * The stored config value is meaningless for this field; load must always overwrite it with the
     * live installed version.
     */
    public function testAfterLoadOverwritesStoredValueWithInstalledVersion(): void
    {
        $this->primeComposerJson(json_encode(['version' => '5.1.0', 'time' => '2026-07-01']));

        $this->model->setValue('stale-value-from-db');

        $result = $this->invokeAfterLoad();

        $this->assertSame($this->model, $result);
        $this->assertSame('5.1.0 (2026-07-01)', $this->model->getValue());
    }

    /**
     * processValue() must ignore whatever is passed and report the installed version, so a value
     * injected via config.php/env.php cannot spoof the reported version.
     */
    public function testProcessValueIgnoresSuppliedValueAndReportsInstalledVersion(): void
    {
        $this->primeComposerJson(json_encode(['version' => '5.1.0', 'time' => '2026-07-01']));

        $this->assertSame('5.1.0 (2026-07-01)', $this->model->processValue('9.9.9-spoofed'));
    }

    public function testProcessValueReturnsFallbackWhenComposerUnreadable(): void
    {
        $this->primeComposerJsonThrows(new FileSystemException(__('Cannot read file')));

        $this->assertStringContainsString('Unknown', (string)$this->model->processValue('anything'));
    }

    /**
     * The version is read from THIS module's composer.json, not the app root's.
     */
    public function testVersionIsReadFromTheCyberSourceModuleComposerJson(): void
    {
        $this->moduleDirMock->expects($this->once())
            ->method('getDir')
            ->with('ParadoxLabs_CyberSource')
            ->willReturn(self::MODULE_DIR);

        $this->fileHandlerMock->expects($this->once())
            ->method('read')
            ->with(self::MODULE_DIR . '/composer.json')
            ->willReturn(json_encode(['version' => '5.1.0']));

        $this->model->_getDefaultValue();
    }

    public function testImplementsProcessorInterface(): void
    {
        $this->assertInstanceOf(
            \Magento\Framework\App\Config\Data\ProcessorInterface::class,
            $this->model
        );
    }
}
