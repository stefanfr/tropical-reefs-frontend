<?php

namespace App\Controller\Api\Checkout\Shipment;

use App\Service\Api\Magento\Checkout\MagentoCheckoutShipmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/checkout/shipment', name: 'api_checkout_shipment')]
class CheckoutShipmentMethodController extends AbstractController
{
    public function __construct(
        protected readonly MagentoCheckoutShipmentService $magentoCheckoutShipmentService
    ) {
    }

    #[Route('/set-method', name: '_set-method', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $error = $this->magentoCheckoutShipmentService->saveShippingMethod(
            $request->get('shipping-method'),
        );

        if ( ! $error) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Shipping method saved successfully',
            ]);
        }

        return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
    }
}