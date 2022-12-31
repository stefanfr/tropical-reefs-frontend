<?php

namespace App\Controller\Customer;

use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountLoginService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountMutationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    public function __construct(
        protected MagentoCustomerAccountMutationService $magentoCustomerAccountMutationService,
    )
    {
    }

    #[Route('/customer/account/login', name: 'app_customer_logout')]
    public function index(Request $request): Response
    {
        $request->getSession()->clear();
        $this->magentoCustomerAccountMutationService->revokeCustomerToken();
    }
}