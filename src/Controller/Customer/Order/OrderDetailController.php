<?php

namespace App\Controller\Customer\Order;

use App\Controller\Customer\AbstractCustomerController;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use App\Service\Api\Magento\Customer\Account\Order\MagentoCustomerOrderQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderDetailController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService         $magentoCustomerAccountService,
        protected MagentoCustomerOrderQueryService $magentoCustomerOrderQueryService,
    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/orders/{orderNumber}', name: 'app_customer_order_detail')]
    public function index(Request $request, string $orderNumber): Response
    {
        $order = $this->magentoCustomerOrderQueryService->collectCustomerOrder($orderNumber);

        return $this->render('customer/order/details.html.twig', [
            'order' => $order,
        ]);
    }
}