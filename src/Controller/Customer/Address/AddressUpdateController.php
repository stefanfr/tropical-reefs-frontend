<?php

namespace App\Controller\Customer\Address;

use App\Controller\Customer\AbstractCustomerController;
use App\Service\Api\Magento\Checkout\MagentoCheckoutApiService;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressUpdateController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService $magentoCustomerAccountService,
        protected readonly MagentoCheckoutApiService $magentoCheckoutApiService,
        protected readonly MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService,
    ) {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/address/update/{addressId}', name: 'app_customer_address_update')]
    public function updateAddress(Request $request, int $addressId): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $customerData = $this->magentoCustomerAccountQueryService->getCustomerData();
        $shippingCountries = $this->magentoCheckoutApiService->collectCountries();
        $address = $this->getAddressById($customerData, $addressId);
        unset($address['id']);

        return $this->render(
            'customer/address/update.html.twig',
            [
                'address' => $address,
                'addressId' => $addressId,
                'shippingCountries' => $shippingCountries,
            ]
        );
    }

    protected function getAddressById(array $customerData, int $addressId): null|array
    {
        return current(
            array_filter($customerData['addresses'], static function ($address) use ($addressId) {
                return $address['id'] === $addressId;
            })
        );
    }
}