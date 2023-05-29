<?php

namespace App\Controller\Customer;

use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCustomerController extends AbstractController
{
    protected string $customerPage = 'default';
    protected array|null $customerData = null;

    public function __construct(
        protected MagentoCustomerAccountQueryService $magentoCustomerAccountQueryService
    ) {
    }

    protected function isAuthenticated(Request $request): bool
    {
        if ( ! $request->getSession()->has('customerToken')) {
            return false;
        }

        $this->customerData = $this->magentoCustomerAccountQueryService->getCustomerData($this->customerPage === 'default');

        return null !== $this->customerData;
    }
}