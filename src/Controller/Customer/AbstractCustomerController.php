<?php

namespace App\Controller\Customer;

use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCustomerController extends AbstractController
{
    public function __construct(
        protected MagentoCustomerAccountQueryService $magentoCustomerAccountService
    )
    {
    }

    protected function isAuthenticated(Request $request): bool
    {
        if ( ! $request->getSession()->has('customerToken')) {
            return false;
        }

        $customerData = $this->magentoCustomerAccountService->getCustomerData();

        return null !== $customerData;
    }
}