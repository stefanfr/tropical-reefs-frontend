<?php

namespace App\Controller\Customer\Address;

use App\Controller\Customer\AbstractCustomerController;
use App\DataClass\Customer\Address\CustomerAddress;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressUpdateController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService              $magentoCustomerAccountService,
        protected MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService,
    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/address/update/{addressId}', name: 'app_customer_address_update')]
    public function updateAddress(Request $request, int $addressId): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $customerData = $this->magentoCustomerAccountService->getCustomerData();

        $addressData = $this->getAddressById($customerData, $addressId);
        $address = new CustomerAddress();
        $address->setId($addressData['id'])
            ->setFirstName($addressData['firstname'])
            ->setLastName($addressData['lastname'])
            ->setCompany($addressData['company'])
            ->setStreet($addressData['street'][0])
            ->setHouseNr($addressData['street'][1] ?? null)
            ->setAdd($addressData['street'][2] ?? null)
            ->setPostcode($addressData['postcode'])
            ->setCity($addressData['city'])
            ->setCountryCode($addressData['country_code'])
            ->setTelephone($addressData['telephone'])
            ->setIsDefaultBilling((int)$customerData['default_billing'] === $addressId)
            ->setIsDefaultShipping((int)$customerData['default_shipping'] === $addressId);

        return $this->render('customer/address/update.html.twig',
            [
                'address' => $address,
            ]
        );
    }

    protected function getAddressById(array $customerData, int $addressId): null|array
    {
        return current(array_filter($customerData['addresses'], static function ($address) use ($addressId) {
            return $address['id'] === $addressId;
        }));
    }
}