<?php

namespace App\Service\Api\Magento\Customer\Account\Wishlist;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\BaseMagentoService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutApiService;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Types\CustomerWishlistType;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCustomerWishlistQueryService extends BaseMagentoService
{
    public function __construct(
        MageGraphQlClient                   $mageGraphQlClient,
        RedisAdapter                        $redisAdapter,
        RequestStack                        $requestStack,
        protected MagentoCheckoutApiService $magentoCheckoutApiService
    )
    {
        $this->mageGraphQlClient = $mageGraphQlClient;
        $this->redisAdapter = $redisAdapter;
        $this->requestStack = $requestStack;

        parent::__construct($mageGraphQlClient, $redisAdapter, $requestStack);
    }

    public function collectCustomerWishlist(bool $formatItems = false): array
    {
        $response = (new Request(
            (new Query('wishlist')
            )->addFields(
                [
                    ...CustomerWishlistType::fields(),
                ]
            ),
            $this->mageGraphQlClient)
        )->send();

        if ($formatItems) {
            $response['data']['wishlist'] = $this->formatItems($response['data']['wishlist'] ?? []);
        }

        return $response['data']['wishlist'] ?? [];
    }

    public function formatItems(array $wishlist): array
    {
        $formattedItems = [];
        foreach ($wishlist['items'] ?? [] as $item) {
            $formattedItems[$item['product']['uid']] = [
                'id' => $item['id'],
                'uid' => $item['product']['uid'],
                'sku' => $item['product']['sku'],
                'quantity' => $item['qty'],
            ];
        }

        $wishlist['items'] = $formattedItems;
        return $wishlist;
    }

}