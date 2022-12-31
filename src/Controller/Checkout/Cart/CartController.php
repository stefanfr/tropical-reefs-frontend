<?php

namespace App\Controller\Checkout\Cart;

use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(
        protected MagentoCheckoutCartApiService    $magentoCheckoutCartApiService,
        protected MagentoCatalogProductApiService  $magentoCatalogProductApiService,
        protected MagentoCatalogCategoryApiService $magentoCatalogCategoryApiService,
    )
    {
    }

    #[Route('/checkout/cart', name: 'app_checkout_cart')]
    public function index(): Response
    {
        $cart = $this->magentoCheckoutCartApiService->collectFullCart();

        if ( ! $cart['total_quantity']) {
            return $this->render('checkout/cart/empty.html.twig', [
                'categories' => $this->getHomeCategories(),
                'featuredProducts' => $this->magentoCatalogProductApiService->collectHomeFeaturedProducts(),
            ]);
        }

        return $this->render('checkout/cart/index.html.twig', [
            'cart' => $cart,
            'totals' => $this->magentoCheckoutCartApiService->formatTotals($cart),
        ]);
    }

    private function getHomeCategories(): array
    {
        $homepageCategories = $this->magentoCatalogCategoryApiService->collectHomeCategories();
        usort($homepageCategories, static function ($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return ($a['homepage_position'] < $b['homepage_position']) ? -1 : 1;
        });

        return $homepageCategories;
    }
}
