<?php

namespace App\Controller\Checkout;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    public function __construct(
        protected MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
    )
    {
    }

    #[Route('/checkout/checkout', name: 'app_checkout_checkout')]
    public function index(): Response
    {
        $cart = $this->magentoCheckoutCartApiService->collectCart();

        if ( ! $cart['total_quantity']) {
            return $this->redirectToRoute('app_checkout_cart');
        }

        return $this->render('checkout/checkout/index.html.twig', [
            'cart' => $cart,
            'totals' => $this->magentoCheckoutCartApiService->formatTotals($cart['prices']),
        ]);
    }
}
