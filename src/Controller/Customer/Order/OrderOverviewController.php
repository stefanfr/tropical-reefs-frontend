<?php

namespace App\Controller\Customer\Order;

use App\Controller\Customer\AbstractCustomerController;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use App\Service\Api\Magento\Customer\Account\Order\MagentoCustomerOrderQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderOverviewController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService         $magentoCustomerAccountService,
        protected MagentoCustomerOrderQueryService $magentoCustomerOrderQueryService,
    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/orders', name: 'app_customer_order_overview')]
    public function orderOverview(Request $request): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $orders = $this->magentoCustomerOrderQueryService->collectCustomerOrders();

        return $this->render('customer/order/overview.html.twig', [
            'customerOrders' => $orders,
        ]);
    }
}