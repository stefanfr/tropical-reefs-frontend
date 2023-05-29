<?php

namespace App\Manager\Customer;

use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerSessionManager
{
    public function __construct(
        protected readonly RequestStack $requestStack,
        protected MagentoCustomerAccountQueryService $magentoCustomerAccountQueryService,
        protected readonly MagentoCustomerAccountMutationService $magentoCustomerAccountMutationService,
    ) {
    }

    public function getCustomer(): ?array
    {
        return $this->magentoCustomerAccountQueryService->getCustomerData();
    }

    public function revokeCustomerSession(): void
    {
        $this->magentoCustomerAccountMutationService->revokeCustomerToken();

        try {
            $session = $this->requestStack->getSession();
            $session->remove('customerToken');
            $session->remove('checkout_quote_mask');
            $session->remove('checkout_cart_item_count');
        } catch (SessionNotFoundException $exception) {
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->requestStack->getSession()?->has('customerToken') ?? false;
    }
}