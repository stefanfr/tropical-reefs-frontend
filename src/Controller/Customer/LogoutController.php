<?php

namespace App\Controller\Customer;

use App\Manager\Customer\CustomerSessionManager;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountLoginService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountMutationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    public function __construct(
        protected RequestStack                          $requestStack,
        protected readonly CustomerSessionManager       $customerSessionManager
    )
    {
    }

    #[Route('/customer/account/logout', name: 'app_customer_logout')]
    public function index(Request $request): Response
    {
        $this->customerSessionManager->revokeCustomerSession();

        return $this->redirectToRoute('app_customer_login');
    }
}