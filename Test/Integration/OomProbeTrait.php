<?php declare(strict_types=1);
/**
 * ParadoxLabs, Inc.
 * https://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  https://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     https://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\CyberSource\Test\Integration;

/**
 * TEMPORARY diagnostic for the Magento 2.4.8-p5 / PHP 8.4 integration leg. REVERT once the cause is found.
 *
 * On that leg alone the suite allocates ~14 GB within a few minutes of starting, exhausts the runner's RAM
 * and swap, and is SIGKILLed by the kernel OOM killer (exit 137). That failure carries no diagnostics at
 * all: php-cli defaults to memory_limit=-1 and Magento's integration phpunit.xml.dist declares no <ini>
 * override, so PHP never raises "Allowed memory size exhausted" — and the runner's line-buffered log
 * reader drops PHPUnit's partial progress line when the process dies on a signal, so not even the test
 * count survives.
 *
 * This trait makes the same runaway legible:
 *
 * - A finite memory_limit turns the OOM kill into an ordinary PHP fatal, which names the file and line the
 *   allocation was attempted from. The limit sits well above anything the suite legitimately uses: the
 *   passing CI legs peak at 391-624 MB, and a local full-suite run with verbose logging peaks at 1.26 GB.
 * - Each test announces itself on STDERR before anything else runs. STDERR is unbuffered, so the last line
 *   printed is the test the process died in — where PHPUnit's own stdout progress is not.
 *
 * @see https://git.paradoxlabs.com/paradoxlabs/m2-extension-cybersource/actions/runs/818
 */
trait OomProbeTrait
{
    /**
     * Cap process memory and announce the running test on STDERR.
     *
     * Call as the FIRST statement of setUp(), ahead of parent::setUp(), so that a wedge inside the parent's
     * own setup — the admin login, the data fixtures — is still attributed to the right test.
     *
     * @return void
     */
    protected function probeMemory(): void
    {
        ini_set('memory_limit', '3G');

        $name = method_exists($this, 'name') ? $this->name() : $this->getName();

        fwrite(STDERR, PHP_EOL . '### OOM-PROBE running ' . static::class . '::' . $name . PHP_EOL);
    }

    /**
     * Report allocated memory at a named point, on STDERR.
     *
     * Sprinkled through the one test that blows up, so the phase it happens in is visible from the CI log
     * even though the process dies before PHPUnit can report anything.
     *
     * @param string $label
     * @return void
     */
    protected function probeMark(string $label): void
    {
        fwrite(
            STDERR,
            sprintf('### OOM-PROBE %9.1f MB  %s' . PHP_EOL, memory_get_usage(true) / 1048576, $label)
        );
    }
}
