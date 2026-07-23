<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Customer\Api\Data\AddressInterface;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\Rest;
use ParadoxLabs\CyberSource\Model\Service\Sanitizer;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CaptureContext;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\Request\CaptureContextRequestFactory;
use ParadoxLabs\TokenBase\Helper\Address;
use Psr\Log\LoggerInterface;

/**
 * Concrete test subclass exposing the abstract sourcing hooks.
 */
class TestableCaptureContext extends CaptureContext
{
    public function __construct(
        Config $config,
        Rest $rest,
        Sanitizer $sanitizer,
        Address $addressHelper,
        CaptureContextRequestFactory $requestFactory,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct(
            config: $config,
            rest: $rest,
            sanitizer: $sanitizer,
            addressHelper: $addressHelper,
            requestFactory: $requestFactory,
            logger: $logger ?? new \Psr\Log\NullLogger()
        );
    }


    public ?string $amount = '24.00';
    public string $currencyCode = 'USD';
    public array $billTo = [];
    public ?string $email = 'jane@example.com';
    public ?int $storeId = 1;
    public array $derivedOrigins = [];

    protected function getAmount(): ?string
    {
        return $this->amount;
    }

    protected function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    protected function getBillTo(): array
    {
        return $this->billTo;
    }

    protected function getEmail(): ?string
    {
        return $this->email;
    }

    protected function getStoreId(): ?int
    {
        return $this->storeId;
    }

    protected function deriveTargetOrigins(): array
    {
        return $this->derivedOrigins;
    }

    public function exposeMapBillTo(?AddressInterface $address): array
    {
        return $this->mapBillTo($address);
    }

    public function exposeNormalizeOrigin(?string $url): ?string
    {
        return $this->normalizeOrigin($url);
    }
}
