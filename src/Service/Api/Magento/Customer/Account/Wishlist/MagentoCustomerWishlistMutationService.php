<?php

namespace App\Service\Api\Magento\Customer\Account\Wishlist;

use App\Cache\Adapter\RedisAdapter;
use App\Manager\Customer\CustomerSessionManager;
use App\Service\Api\Magento\BaseMagentoService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutApiService;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;
use App\Service\GraphQL\Types\CustomerWishlistType;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCustomerWishlistMutationService extends BaseMagentoService
{
    public function __construct(
        MageGraphQlClient                            $mageGraphQlClient,
        RedisAdapter                                 $redisAdapter,
        RequestStack                                 $requestStack,
        protected readonly CustomerSessionManager    $customerSessionManager,
        protected readonly MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
        $this->mageGraphQlClient = $mageGraphQlClient;
        $this->redisAdapter = $redisAdapter;
        $this->requestStack = $requestStack;

        parent::__construct($mageGraphQlClient, $redisAdapter, $requestStack);
    }

    public function addProductToWishlist(string $productSku, int $productQty): array|bool
    {
        $customer = $this->customerSessionManager->getCustomer();
        $wishlistId = current($customer['wishlists'])['id'] ?? null;
        if (null === $wishlistId) {
            $wishlistId = 0;
        }
        $response = (new Request(
            (new Mutation('addProductsToWishlist')
            )->addParameters(
                [
                    new InputField('wishlistId', $wishlistId),
                    (new InputField('wishlistItems')
                    )->addChildInputFields(
                        [
                            new InputField('sku', $productSku),
                            new InputField('quantity', $productQty),
                        ]
                    ),
                ]
            )->addFields(
                [
                    (new Field('wishlist')
                    )->addChildFields(
                        [
                            ...CustomerWishlistType::fields(),
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        )
        )->send();

        return $response['data']['addProductsToWishlist']['wishlist'] ?? false;
    }

    public function removeProductFromWishlist(string $itemId): array|bool
    {
        $customer = $this->customerSessionManager->getCustomer();
        $wishlistId = current($customer['wishlists'])['id'] ?? null;
        if (null === $wishlistId) {
            $wishlistId = 0;
        }
        $response = (new Request(
            (new Mutation('removeProductsFromWishlist')
            )->addParameters(
                [
                    new InputField('wishlistId', $wishlistId),
                    (new InputField('wishlistItemsIds', [
                        $itemId,
                    ])),
                ]
            )->addFields(
                [
                    (new Field('wishlist')
                    )->addChildFields(
                        [
                            ...CustomerWishlistType::fields(),
                        ]
                    ),
                ]
            ),
            $this->mageGraphQlClient
        )
        )->send();

        return $response['data']['removeProductsFromWishlist']['wishlist'] ?? false;
    }

    public function mergeWishlist(): void
    {
        $session = $this->requestStack->getSession();

        foreach (array_column($session->get('wishlistItems') ?? [], 'sku') as $wishlistProductSku) {
            $this->addProductToWishlist($wishlistProductSku, 1);
        }

        $session->remove('wishlistItems');
        $session->remove('wishlistItemCount');
    }
}