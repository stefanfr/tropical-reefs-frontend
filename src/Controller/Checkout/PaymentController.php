<?php

namespace App\Controller\Checkout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractCheckoutController
{
    #[Route('/checkout/payment', name: 'app_checkout_payment')]
    public function index(): Response
    {
        $cart = $this->magentoCheckoutCartApiService->collectFullCart();
        $useCoupon = false;

        if (null !== $cart['applied_coupons']) {
            $useCoupon = true;
            $couponIsValid = true;
            $couponCode = current($cart['applied_coupons'])['code'];
        }

        $totals = $this->magentoCheckoutCartApiService->formatTotals($cart);

        return $this->render('checkout/checkout/payment.html.twig', [
            'totals' => $totals,
            'useCoupon' => $useCoupon,
            'cartItems' => $cart['items'],
            'couponCode' => $couponCode ?? null,
            'couponIsValid' => $couponIsValid ?? false,
            'paymentMethods' => $cart['available_payment_methods'],
            'selectedPaymentMethod' => $cart['selected_payment_method']['code'],
        ]);
    }
}
