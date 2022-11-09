<?php

namespace App\Controller\Checkout\Cart;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(
        protected MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
    )
    {
    }

    #[Route('/checkout/cart', name: 'app_checkout_cart')]
    public function index(): Response
    {
        return $this->render('checkout/cart/index.html.twig', [
            'cart' => $this->magentoCheckoutCartApiService->collectCart(),
        ]);
    }
}
