<?php

namespace App\Controller\Customer\Address;

use App\Controller\Customer\AbstractCustomerController;
use App\DataClass\Customer\Address\CustomerAddress;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressCreateController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService              $magentoCustomerAccountService,
        protected MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService,
    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/address/create', name: 'app_customer_address_create')]
    public function createAddress(Request $request): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $address = new CustomerAddress();

        return $this->render('customer/address/create.html.twig',
            [
                'address' => $address,
            ]
        );
    }
}