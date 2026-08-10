<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

/**
 * Loads the pinned /risk/v1 reply fixtures captured from the live sandbox 2026-08-04.
 */
trait FixtureLoaderTrait
{
    /**
     * Decode a JSON fixture from _files/ into an array.
     *
     * @param string $name Fixture basename without extension.
     * @return array<string, mixed>
     */
    protected function loadFixture(string $name): array
    {
        $path = __DIR__ . '/_files/' . $name . '.json';

        $this->assertFileExists($path);

        $decoded = json_decode((string)file_get_contents($path), true);

        $this->assertIsArray($decoded, 'Fixture ' . $name . ' must decode to an array.');

        return $decoded;
    }
}
