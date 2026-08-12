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
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterface;
use ParadoxLabs\CyberSource\Api\Data\PayerAuthBrowserInfoInterfaceFactory;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Management;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\ManagementFactory;
use ParadoxLabs\TokenBase\Model\Api\GraphQL;
use ParadoxLabs\CyberSource\Helper\Data;

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
     * Default ports elided when reducing a URL to its origin.
     */
    private const DEFAULT_PORTS = [
        'https' => 443,
        'http' => 80,
    ];

    /**
     * Authenticate constructor.
     *
     * @param GraphQL $graphQL
     * @param ManagementFactory $managementFactory
     * @param Config $config
     * @param Data $helper
     * @param PayerAuthBrowserInfoInterfaceFactory $browserInfoFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GraphQL $graphQL,
        ManagementFactory $managementFactory,
        Config $config,
        Data $helper,
        private readonly PayerAuthBrowserInfoInterfaceFactory $browserInfoFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
        parent::__construct($graphQL, $managementFactory, $config, $helper);
    }

    /**
     * Run the authentication for the authorized cart.
     *
     * @param Management $management
     * @param array<string, mixed> $input
     * @param CartInterface $quote
     * @return array<string, mixed>
     * @throws GraphQlInputException
     */
    protected function execute(Management $management, array $input, CartInterface $quote): array
    {
        $browserInfo = $this->buildBrowserInfo($input['browserInfo'] ?? null);
        $returnUrl   = $this->stringOrNull($input['returnUrl'] ?? null);

        if ($returnUrl === null) {
            // No URL supplied: the service applies its own store-hosted default, which is correct
            // for a client that has none of its own.
            return $this->resultPayload($management->authenticate($browserInfo, null));
        }

        return $this->resultPayload(
            $management->authenticateWithValidatedReturnUrl(
                $browserInfo,
                $this->validateReturnUrl($returnUrl, (int)$quote->getStoreId())
            )
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
     * Validate a client-supplied return URL against shape rules and the origin allowlist.
     *
     * Shape: absolute https URL parsing cleanly to a plain host, with no userinfo component — the
     * classic "https://store.example.com@evil.example/" spoof. Origin: the store's own secure
     * base-URL origin is always accepted; anything else must be listed in the merchant's
     * `payer_auth_return_origins` config. An empty list therefore means same-store-origin only —
     * fail closed, since an unlisted foreign origin is an open redirect target for the ACS POST.
     *
     * @param string $returnUrl
     * @param int $storeId
     * @return string
     * @throws GraphQlInputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateReturnUrl(string $returnUrl, int $storeId): string
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

        $origin = $this->normalizeOrigin($returnUrl);

        if ($origin === null || !in_array($origin, $this->permittedOrigins($storeId), true)) {
            throw new GraphQlInputException(
                __(
                    'The return URL origin is not permitted. Add it to the CyberSource payment'
                    . ' method\'s "Headless Return URL Origins" setting.'
                )
            );
        }

        return $returnUrl;
    }

    /**
     * Get every origin permitted as a return target for this store.
     *
     * @param int $storeId
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function permittedOrigins(int $storeId): array
    {
        $baseUrl = (string)$this->storeManager->getStore($storeId)
            ->getBaseUrl(UrlInterface::URL_TYPE_LINK, true);

        $origins = [$this->normalizeOrigin($baseUrl)];

        foreach ($this->config->getPayerAuthReturnOrigins($storeId) as $configured) {
            $origins[] = $this->normalizeOrigin($configured);
        }

        return array_values(array_unique(array_filter($origins)));
    }

    /**
     * Reduce a URL to its origin (scheme://host[:port]), with the scheme's default port elided.
     *
     * @param string $url
     * @return string|null
     */
    private function normalizeOrigin(string $url): ?string
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction -- parsing a URL, not fetching it.
        $parts = parse_url($url);
        // phpcs:enable Magento2.Functions.DiscouragedFunction

        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower((string)$parts['scheme']);
        $origin = $scheme . '://' . strtolower((string)$parts['host']);
        $port   = isset($parts['port']) ? (int)$parts['port'] : null;

        if ($port !== null && $port !== (self::DEFAULT_PORTS[$scheme] ?? null)) {
            $origin .= ':' . $port;
        }

        return $origin;
    }
}
