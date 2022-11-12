<?php

namespace App\Controller\Checkout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentController extends AbstractCheckoutController
{
    #[Route('/checkout/shipment', name: 'app_checkout_shipment')]
    public function index(): Response
    {
        $cart = $this->magentoCheckoutCartApiService->collectFullCart();

        return $this->render('checkout/checkout/shipment.html.twig', [
            'shippingMethods' => $cart['shipping_addresses'][0]['available_shipping_methods'] ?? [],
            'selectedShippingMethod' => $cart['shipping_addresses'][0]['selected_shipping_method'] ?? null,
        ]);
    }
}
