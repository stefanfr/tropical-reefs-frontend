<?php

namespace App\Controller\Checkout;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
use App\Service\Api\Magento\Customer\Account\Order\MagentoCustomerOrderQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SuccessController extends AbstractCheckoutController
{
    public function __construct(
        MagentoCheckoutCartApiService              $magentoCheckoutCartApiService,
        protected MagentoCustomerOrderQueryService $magentoCustomerOrderQueryService,
        protected MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    )
    {
        $this->magentoCheckoutCartApiService = $magentoCheckoutCartApiService;
        parent::__construct($this->magentoCheckoutCartApiService);
    }

    #[Route('/checkout/success', name: 'app_checkout_success')]
    public function success(Request $request)
    {
        if ( ! $request->getSession()->has('lastOrderNumber')) {
            return $this->redirectToRoute('app_home');
        }

        $orderNumber = $request->getSession()->get('lastOrderNumber');
        $order = $this->magentoCheckoutPaymentApiService->collectOrder($orderNumber);
        $request->getSession()->remove('lastOrderNumber');

        return $this->render('checkout/checkout/success.html.twig', [
            'order' => $order,
        ]);
    }
}