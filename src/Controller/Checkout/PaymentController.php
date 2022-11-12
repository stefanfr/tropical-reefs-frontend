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

        return $this->render('checkout/checkout/payment.html.twig', [
            'paymentMethods' => $cart['available_payment_methods'],
            'selectedPaymentMethod' => $cart['selected_payment_method']['code'],
        ]);
    }
}
