<?php

namespace ParadoxLabs\CyberSource\Gateway\Api;

class ProviderFields
{

    /**
     * @var Provider[] $provider
     */
    protected $provider = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Provider[]
     */
    public function getProvider()
    {
      return $this->provider;
    }

    /**
     * @param Provider[] $provider
     * @return \ParadoxLabs\CyberSource\Gateway\Api\ProviderFields
     */
    public function setProvider(array $provider = null)
    {
      $this->provider = $provider;
      return $this;
    }

}
