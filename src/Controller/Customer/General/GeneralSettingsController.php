<?php

namespace App\Controller\Customer\General;

use App\Controller\Customer\AbstractCustomerController;
use App\DataClass\Customer\CustomerAccount;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralSettingsController extends AbstractCustomerController
{

    #[Route('/customer/general', name: 'app_customer_general')]
    public function generalSettings(Request $request): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $customerDetails = [
            'firstname' => $this->customerData['firstname'],
            'lastname' => $this->customerData['lastname'],
            'email' => $this->customerData['email'],
        ];

        return $this->render(
            'customer/general/index.html.twig',
            [
                'customerDetails' => $customerDetails,
            ]
        );
    }
}