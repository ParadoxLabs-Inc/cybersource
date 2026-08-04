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

namespace ParadoxLabs\CyberSource\Model\Api\GraphQL\PayerAuth;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterfaceFactory;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ManagementFactory;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use Psr\Log\LoggerInterface;

/**
 * GraphQL (headless storefront) Payer Authentication authenticate resolver.
 *
 * Runs the authentication with client-collected browser data. Server-derived fields (user agent,
 * accept header, IP) are never taken from input; only the browser values the client alone knows.
 */
class Authenticate extends AbstractResolver
{
    /**
     * Browser fields required by the card networks; all are client-collected.
     */
    private const BROWSER_FIELDS = [
        'language',
        'javaEnabled',
        'javaScriptEnabled',
        'colorDepth',
        'screenHeight',
        'screenWidth',
        'timeDifference',
    ];

    /**
     * Authenticate constructor.
     *
     * @param GraphQL $graphQL
     * @param ManagementFactory $managementFactory
     * @param Config $config
     * @param LoggerInterface $logger
     * @param PayerAuthBrowserInfoInterfaceFactory $browserInfoFactory
     */
    public function __construct(
        GraphQL $graphQL,
        ManagementFactory $managementFactory,
        Config $config,
        LoggerInterface $logger,
        private readonly PayerAuthBrowserInfoInterfaceFactory $browserInfoFactory
    ) {
        parent::__construct($graphQL, $managementFactory, $config, $logger);
    }

    /**
     * Run the authentication for the authorized cart.
     *
     * @param Management $management
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     * @throws GraphQlInputException
     */
    protected function execute(Management $management, array $input): array
    {
        $browserInfo = $this->buildBrowserInfo($input['browserInfo'] ?? null);
        $returnUrl   = $this->stringOrNull($input['returnUrl'] ?? null);

        if ($returnUrl === null) {
            // No URL supplied: the service applies its own store-hosted default, which is correct
            // for a client that has none of its own.
            return $this->resultPayload($management->authenticate($browserInfo, null));
        }

        return $this->resultPayload(
            $management->authenticateWithValidatedReturnUrl($browserInfo, $this->validateReturnUrl($returnUrl))
        );
    }

    /**
     * Build the browser-info DTO from client input, requiring the full set the networks demand.
     *
     * @param mixed $input
     * @return PayerAuthBrowserInfoInterface
     * @throws GraphQlInputException
     */
    private function buildBrowserInfo(mixed $input): PayerAuthBrowserInfoInterface
    {
        if (!is_array($input)) {
            throw new GraphQlInputException(__('Required parameter "browserInfo" is missing.'));
        }

        foreach (self::BROWSER_FIELDS as $key) {
            if (!array_key_exists($key, $input) || $input[$key] === null || $input[$key] === '') {
                throw new GraphQlInputException(
                    __('Required browser information "%1" is missing.', $key)
                );
            }
        }

        /** @var PayerAuthBrowserInfoInterface $browserInfo */
        $browserInfo = $this->browserInfoFactory->create();

        return $browserInfo->setLanguage((string)$input['language'])
            ->setJavaEnabled((bool)$input['javaEnabled'])
            ->setJavaScriptEnabled((bool)$input['javaScriptEnabled'])
            ->setColorDepth((int)$input['colorDepth'])
            ->setScreenHeight((int)$input['screenHeight'])
            ->setScreenWidth((int)$input['screenWidth'])
            ->setTimeDifference((int)$input['timeDifference']);
    }

    /**
     * Validate a client-supplied return URL.
     *
     * A headless storefront runs on its own origin, so a foreign host is legitimate here (unlike
     * the session-backed surfaces, which require the store's own host). The URL must still be an
     * absolute https URL that parses cleanly to a plain host, with no userinfo component — the
     * classic "https://store.example.com@evil.example/" spoof.
     *
     * @param string $returnUrl
     * @return string
     * @throws GraphQlInputException
     */
    private function validateReturnUrl(string $returnUrl): string
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction -- validating a URL, not fetching it.
        $parts = parse_url($returnUrl);
        // phpcs:enable Magento2.Functions.DiscouragedFunction

        $host = is_array($parts) && isset($parts['host']) ? (string)$parts['host'] : '';

        if (!is_array($parts)
            || ($parts['scheme'] ?? '') !== 'https'
            || isset($parts['user'])
            || isset($parts['pass'])
            || preg_match('/^[A-Za-z0-9]([A-Za-z0-9\-.]*[A-Za-z0-9])?$/', $host) !== 1
            || filter_var($returnUrl, FILTER_VALIDATE_URL) === false) {
            throw new GraphQlInputException(
                __('The return URL must be an absolute, secure (https) URL.')
            );
        }

        return $returnUrl;
    }
}
