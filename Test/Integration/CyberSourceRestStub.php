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

use Exception;
use LogicException;
use ParadoxLabs\CyberSource\Model\Service\Rest;

/**
 * Test double for the CyberSource REST HTTP boundary.
 *
 * The real {@see Rest} class signs and dispatches live cURL calls to CyberSource. This double replaces it via
 * ObjectManager::addSharedInstance so the whole Method + Gateway + UnifiedCheckout service stack executes
 * against canned replies. Every call is recorded for assertions, and response shaping is delegated to a
 * caller-supplied responder: fn(string $method, string $path, array $params): array|string.
 *
 * Return types deliberately mirror the real client, because callers depend on the difference:
 *  - post()    → array  (decoded JSON)
 *  - postRaw() → string (a bare JWT, e.g. the Unified Checkout capture context)
 *  - get()/delete() → string (raw body; DELETE is a 204 with an empty body)
 *
 * To simulate a gateway/HTTP failure, throw from the responder exactly as the real client does on a non-2xx
 * response — `new Exception($message, $httpStatus)` — or use {@see httpError()}.
 */
class CyberSourceRestStub extends Rest
{
    /**
     * Recorded calls, in order: ['method' => ..., 'path' => ..., 'params' => [...]].
     *
     * @var array<int, array{method: string, path: string, params: array}>
     */
    public array $calls = [];

    /**
     * @var callable|null
     */
    private $responder;

    /**
     * Bypass the parent constructor: none of the real collaborators (config, HTTP, signing) are needed here.
     */
    public function __construct()
    {
    }

    /**
     * Configure the response shaper used for every HTTP verb.
     *
     * @param callable $responder fn(string $method, string $path, array $params): array|string
     * @return void
     */
    public function setResponder(callable $responder): void
    {
        $this->responder = $responder;
    }

    /**
     * Build the exception the real client raises for a non-2xx response, for use from a responder.
     *
     * @param string $message
     * @param int $status
     * @return Exception
     */
    public static function httpError(string $message, int $status = 400): Exception
    {
        return new Exception($message, $status);
    }

    /**
     * Return the recorded request paths in call order.
     *
     * @return array<int, string>
     */
    public function getCalledPaths(): array
    {
        return array_column($this->calls, 'path');
    }

    /**
     * Return the recorded calls whose path contains the given needle.
     *
     * @param string $needle
     * @return array<int, array{method: string, path: string, params: array}>
     */
    public function getCallsMatching(string $needle): array
    {
        return array_values(
            array_filter($this->calls, static fn (array $call): bool => str_contains($call['path'], $needle))
        );
    }

    /**
     * Forget all recorded calls, so a test can assert on a single phase of a multi-step flow.
     *
     * @return void
     */
    public function resetCalls(): void
    {
        $this->calls = [];
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string
     */
    #[\Override]
    public function get($path, $params = [], $responseType = 'application/hal+json')
    {
        return (string)$this->respond('GET', (string)$path, (array)$params);
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string
     */
    #[\Override]
    public function delete($path, $params = [], $responseType = 'application/hal+json')
    {
        return (string)$this->respond('DELETE', (string)$path, (array)$params);
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return array
     */
    #[\Override]
    public function post(string $path, array $params = [], string $responseType = 'application/json'): array
    {
        return (array)$this->respond('POST', $path, $params);
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $responseType
     * @return string
     */
    #[\Override]
    public function postRaw(string $path, array $params = [], string $responseType = 'application/jwt'): string
    {
        return (string)$this->respond('POST', $path, $params);
    }

    /**
     * Record the call and delegate to the configured responder.
     *
     * @param string $method
     * @param string $path
     * @param array $params
     * @return array|string
     */
    private function respond(string $method, string $path, array $params)
    {
        $this->calls[] = [
            'method' => $method,
            'path' => $path,
            'params' => $params,
        ];

        if ($this->responder === null) {
            throw new LogicException(
                sprintf('CyberSourceRestStub has no responder configured for %s %s', $method, $path)
            );
        }

        return ($this->responder)($method, $path, $params);
    }
}
