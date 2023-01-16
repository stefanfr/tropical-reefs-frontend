<?php

namespace App\Controller\Customer;

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
        protected MagentoCustomerAccountMutationService $magentoCustomerAccountMutationService,
    )
    {
    }

    #[Route('/customer/account/logout', name: 'app_customer_logout')]
    public function index(Request $request): Response
    {
        try {
            $session = $this->requestStack->getSession();
            $session->remove('customerToken');
            $session->remove('checkout_quote_mask');
            $session->remove('checkout_cart_item_count');
        } catch (SessionNotFoundException $exception) {

        }

        $this->magentoCustomerAccountMutationService->revokeCustomerToken();

        return $this->redirectToRoute('app_customer_login');
    }
}