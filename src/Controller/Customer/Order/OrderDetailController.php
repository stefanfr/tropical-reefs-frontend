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
        MagentoCustomerAccountQueryService $magentoCustomerAccountService,
        protected readonly MagentoCustomerOrderQueryService $magentoCustomerOrderQueryService,
    ) {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/orders/{orderNumber}', name: 'app_customer_order_detail')]
    public function index(Request $request, string $orderNumber): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $order = $this->magentoCustomerOrderQueryService->collectCustomerOrder($orderNumber);

        if ( ! $order) {
            return $this->redirectToRoute('app_customer_order_overview');
        }

        return $this->render('customer/order/details.html.twig', [
            'order' => $this->formatOrder($order),
        ]);
    }

    protected function formatOrder(array $order): array
    {
        $order['totals'] = $this->formatTotals($order['totals']);

        return $order;
    }

    protected function formatTotals(array $totals): array
    {
        $cartTotals = [];

        $prices = array_filter($totals, static function ($total) {
            return ! empty($total);
        });

        foreach ($prices as $key => $price) {
            $cartTotals[] = [
                'grandTotal' => $key === 'grand_total',
                'label' => ucfirst(str_replace('_', ' ', $key)),
                'value' => $price['value'],
                'currency' => $price['currency'],
            ];
        }

        return $cartTotals;
    }
}