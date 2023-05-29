<?php

namespace App\Controller\Api\Catalog\Product;

use App\Manager\Customer\CustomerSessionManager;
use App\Service\Api\Magento\Customer\Account\Wishlist\MagentoCustomerWishlistMutationService;
use App\Service\Api\Magento\Customer\Account\Wishlist\MagentoCustomerWishlistQueryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog', name: 'api_catalog_product')]
class AddProductToWishlist extends AbstractController
{
    public function __construct(
        protected readonly RequestStack                           $requestStack,
        protected readonly CustomerSessionManager                 $customerSessionManager,
        protected readonly MagentoCustomerWishlistQueryService    $magentoCustomerWishlistQueryService,
        protected readonly MagentoCustomerWishlistMutationService $magentoCustomerWishlistMutationService,
    )
    {
    }

    #[Route('/wishlist/product', name: '_wishlist_product', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        if ($this->customerSessionManager->isLoggedIn()) {
            $wishlist = $this->magentoCustomerWishlistMutationService->addProductToWishlist(
                $request->get('sku'),
                1
            );
            if (!is_bool($wishlist)) {
                return new JsonResponse([
                        'status' => 'success',
                        'content' => 'Product added to wishlist successfully',
                        'wishlist' => $this->magentoCustomerWishlistQueryService->formatItems($wishlist),
                    ]
                );
            }

            return new JsonResponse([
                'status' => 'error',
                'content' => 'Unable to add product to wishlist',
            ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $session = $this->requestStack->getSession();
        $wishlistItems = $session->get('wishlistItems', []);

        if (!array_key_exists($request->get('uid'), $wishlistItems)) {
            $wishlistItems[$request->get('uid')] = [
                'uid' => $request->get('uid'),
                'sku' => $request->get('sku'),
                'quantity' => 1,
            ];
        }

        $session->set('wishlistItems', $wishlistItems);
        $session->set('wishlistItemCount', count($wishlistItems));

        return new JsonResponse([
                'status' => 'success',
                'content' => 'Product added to wishlist successfully',
                'wishlist' => [
                    'items_count' => count($wishlistItems),
                    'items' => $wishlistItems,
                ]
            ]
        );
    }
}