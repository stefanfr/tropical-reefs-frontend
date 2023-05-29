<?php

namespace App\Controller\Customer\Wishlist;

use App\Controller\Customer\AbstractCustomerController;
use App\Manager\Customer\CustomerSessionManager;
use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use App\Service\Api\Magento\Customer\Account\Wishlist\MagentoCustomerWishlistMutationService;
use App\Service\Api\Magento\Customer\Account\Wishlist\MagentoCustomerWishlistQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishlistController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService $magentoCustomerAccountService,
        protected readonly RequestStack $requestStack,
        protected readonly CustomerSessionManager $customerSessionManager,
        protected readonly MagentoCatalogProductApiService $magentoCatalogProductApiService,
        protected readonly MagentoCustomerWishlistQueryService $magentoCustomerWishlistQueryService,
        protected readonly MagentoCustomerWishlistMutationService $magentoCustomerWishlistMutationService,
    ) {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/account/wishlist', name: 'app_customer_wishlist')]
    public function index(Request $request): Response
    {
        $breadcrumbs[] = [
            'active' => true,
            'label' => 'Wishlist',
            'url' => $this->generateUrl('app_customer_wishlist'),
        ];

        if ( ! $this->isAuthenticated($request)) {
            return $this->guestWishlist($breadcrumbs);
        }

        $wishlist = $this->magentoCustomerWishlistQueryService->collectCustomerWishlist();

        $wishlist['items'] = array_map(static function (array $wishlistItem) {
            $productData = $wishlistItem['product'];
            unset($wishlistItem['product']);

            return [
                ...$wishlistItem,
                ...$productData,
            ];
        }, $wishlist['items'] ?? []);

        return $this->render('customer/wishlist/index.html.twig', [
            'loggedIn' => $this->customerSessionManager->isLoggedIn(),
            'breadcrumbs' => $breadcrumbs,
            'wishlist' => $wishlist,
        ]);
    }

    protected function guestWishlist(array $breadcrumbs): Response
    {
        $session = $this->requestStack->getSession();

        $products = $this->magentoCatalogProductApiService->collectProducts(array_column($session->get('wishlistItems') ?? [], 'uid'));

        return $this->render('customer/wishlist/index.html.twig', [
            'loggedIn' => $this->customerSessionManager->isLoggedIn(),
            'breadcrumbs' => $breadcrumbs,
            'wishlist' => [
                'items' => $products,
            ],
        ]);
    }
}