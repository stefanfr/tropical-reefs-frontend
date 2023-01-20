<?php

namespace App\Controller\Checkout;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractCheckoutController
{
    public function __construct(
        MagentoCheckoutCartApiService              $magentoCheckoutCartApiService,
        protected MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    )
    {
        $this->magentoCheckoutCartApiService = $magentoCheckoutCartApiService;

        parent::__construct($this->magentoCheckoutCartApiService);
    }

    #[Route('/checkout/payment', name: 'app_checkout_payment')]
    public function index(Request $request): Response
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

    #[Route('/checkout/payment/complete', name: 'app_checkout_payment_complete')]
    public function complete(HttpRequest $request)
    {
        $payOrderId = $request->query->get('orderId');
        $orderResponse = $this->magentoCheckoutPaymentApiService->finalizeOrder($payOrderId);

        if ($orderResponse && $orderResponse['isSuccess']) {
            $request->getSession()->set('lastOrderNumber', $orderResponse['orderNumber']);
            return $this->redirectToRoute('app_checkout_success');
        }

        //TODO re-enable quote
        return $this->redirectToRoute('app_checkout_cart');
    }
}
