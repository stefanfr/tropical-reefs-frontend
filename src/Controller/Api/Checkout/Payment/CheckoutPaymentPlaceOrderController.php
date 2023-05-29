<?php

namespace App\Controller\Api\Checkout\Payment;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/checkout/payment', name: 'api_checkout_payment')]
class CheckoutPaymentPlaceOrderController extends AbstractController
{
    public function __construct(
        protected readonly MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
        protected readonly MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    ) {
    }

    #[Route('/set-method', name: '_set_method', methods: ['POST'])]
    public function setMethod(Request $request): JsonResponse
    {
        $errors = $this->magentoCheckoutPaymentApiService->savePaymentMethod($request->get('method_code'));

        if ( ! $errors) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    #[Route('/place', name: '_pace_order', methods: ['POST'])]
    public function placeOrder(Request $request): JsonResponse
    {
        $orderNumber = $this->magentoCheckoutPaymentApiService->placeOrder();

        if (is_string($orderNumber)) {
            $errors = $this->magentoCheckoutPaymentApiService->startPayNlTransaction(
                $orderNumber,
                $request->getUriForPath($this->generateUrl('app_checkout_payment_complete'))
            );

            if (is_string($errors)) {
                return new JsonResponse(
                    [
                        'redirect_url' => $errors,
                    ]
                );
            }
        }

        return new JsonResponse($orderNumber, Response::HTTP_BAD_REQUEST);
    }
}