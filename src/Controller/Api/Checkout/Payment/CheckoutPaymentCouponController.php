<?php

namespace App\Controller\Api\Checkout\Payment;

use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/checkout/payment', name: 'api_checkout_payment')]
class CheckoutPaymentCouponController extends AbstractController
{
    public function __construct(
        protected readonly MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    ) {
    }

    #[Route('/coupon/{couponCode}', name: '_apply_coupon')]
    public function applyCoupon(string $couponCode): JsonResponse
    {
        $applyCoupon = $this->magentoCheckoutPaymentApiService->applyCoupon($couponCode);

        if (isset($applyCoupon['errors'])) {
            return new JsonResponse($applyCoupon['errors'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(null);
    }

    #[Route('/coupon', name: '_remove_coupon', methods: ['DELETE'])]
    public function removeCoupon(): JsonResponse
    {
        $applyCoupon = $this->magentoCheckoutPaymentApiService->removeCoupon();

        if (isset($applyCoupon['errors'])) {
            return new JsonResponse($applyCoupon['errors'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(null);
    }
}