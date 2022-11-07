<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCheckoutAddressApiService
{
    public function __construct(
        protected RedisAdapter      $redisAdapter,
        protected RequestStack      $requestStack,
        protected MageGraphQlClient $mageGraphQlClient,
    )
    {
    }
}