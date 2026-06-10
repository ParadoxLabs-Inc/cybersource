<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\Customer\Api\Data\AddressInterface;
use ParadoxLabs\CyberSource\Model\Service\UnifiedCheckout\CaptureContext;

/**
 * Concrete test subclass exposing the abstract sourcing hooks.
 */
class TestableCaptureContext extends CaptureContext
{
    public ?string $amount = '24.00';
    public string $currencyCode = 'USD';
    public array $billTo = [];
    public ?string $email = 'jane@example.com';
    public ?int $storeId = 1;
    public bool $saveCard = false;
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

    protected function canRequestSaveCard(): bool
    {
        return $this->saveCard;
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
