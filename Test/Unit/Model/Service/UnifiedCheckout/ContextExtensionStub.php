<?php

declare(strict_types=1);

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\UnifiedCheckout;

use Magento\GraphQl\Model\Query\ContextExtension;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Hand-written GraphQL context extension stub with a guaranteed store attribute.
 *
 * The generated {@see ContextExtension} cannot be used directly: whether it exposes getStore()
 * depends on which extension_attributes.xml files were processed at generation time, and in CI it
 * is generated WITHOUT the store attribute — getStore() then doesn't exist, the service's
 * defensive \Throwable catches swallow the Error, and store-derived values silently vanish.
 * Extending the generated class keeps the ContextInterface::getExtensionAttributes() return type
 * satisfied while pinning getStore()/setStore() to real, environment-independent implementations.
 * (MockBuilder::addMethods() is not an option; it was removed in PHPUnit 12.)
 */
class ContextExtensionStub extends ContextExtension
{
    /**
     * @var StoreInterface|null
     */
    private ?StoreInterface $store = null;

    /**
     * Get the store for this context.
     *
     * @return StoreInterface|null
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set the store for this context.
     *
     * @param StoreInterface $store
     * @return $this
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;

        return $this;
    }
}
