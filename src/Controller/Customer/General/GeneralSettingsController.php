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

        $customerData = $this->magentoCustomerAccountService->getCustomerData();

        $customerAccount = (new CustomerAccount()
        )->setEmail($customerData['email'])
            ->setFirstname($customerData['firstname'])
            ->setLastname($customerData['lastname']);

        return $this->render('customer/general/index.html.twig',
            [
                'customerAccount' => $customerAccount,
                'customerPassword' => (new CustomerAccount())->setPassword('test'),
            ]
        );
    }
}