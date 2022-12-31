<?php

namespace App\Controller\Customer\Address;

use App\Controller\Customer\AbstractCustomerController;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressDeleteController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService              $magentoCustomerAccountService,
        protected MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService,
    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/address/delete/{addressId}', name: 'app_customer_address_delete')]
    public function updateAddress(Request $request, int $addressId): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $errors = $this->magentoCustomerAddressMutationService->deleteAddress($addressId);
        foreach ($errors as $error) {
            $this->addFlash('error', $error);
        }

        return $this->redirectToRoute('app_customer_address');
    }

    protected function getAddressById(array $customerData, int $addressId): null|array
    {
        return current(array_filter($customerData['addresses'], static function ($address) use ($addressId) {
            return $address['id'] === $addressId;
        }));
    }
}