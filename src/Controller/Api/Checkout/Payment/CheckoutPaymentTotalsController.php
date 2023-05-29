<?php

namespace App\Controller\Api\Checkout\Payment;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/checkout/payment', name: 'api_checkout_payment')]
class CheckoutPaymentTotalsController extends AbstractController
{
    public function __construct(
        protected readonly MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
        protected readonly MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    ) {
    }

    #[Route('/totals', name: '_totals', methods: ['GET'])]
    public function collectTotals(Request $request): JsonResponse
    {
        $cart = $this->magentoCheckoutCartApiService->collectFullCart();
        $cart['totals'] = $this->magentoCheckoutCartApiService->formatTotals($cart);

        return new JsonResponse($cart);
    }
}