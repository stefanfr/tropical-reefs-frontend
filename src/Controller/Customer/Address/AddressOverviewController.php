<?php

namespace App\Controller\Customer\Address;

use App\Controller\Customer\AbstractCustomerController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressOverviewController extends AbstractCustomerController
{

    #[Route('/customer/address', name: 'app_customer_address')]
    public function index(Request $request): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $customerData = $this->magentoCustomerAccountService->getCustomerData();

        return $this->render('customer/address/overview.html.twig', [
            'customerData' => $customerData,
        ]);
    }
}