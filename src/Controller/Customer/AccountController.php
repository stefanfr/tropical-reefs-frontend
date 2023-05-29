<?php

namespace App\Controller\Customer;

use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use App\Service\Api\Magento\Customer\Account\Wishlist\MagentoCustomerWishlistQueryService;
use App\Trait\Customer\CustomerAuthenticationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractCustomerController
{
    public function __construct(
        MagentoCustomerAccountQueryService              $magentoCustomerAccountService,
        protected MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService,
        protected readonly MagentoCustomerWishlistQueryService $magentoCustomerWishlistQuery
    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/account', name: 'app_customer')]
    public function index(Request $request): Response
    {
        if ( ! $this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer_login');
        }

        $customerData = $this->magentoCustomerAccountQueryService->getCustomerData();

        return $this->render('customer/index.html.twig', [
            'customerData' => $customerData,
            'customerOrders' => $customerData['orders']['items'],
            'defaultShippingAddress' => $this->getDefaultSelectedAddress($customerData),
            'defaultBillingAddress' => $this->getDefaultSelectedAddress($customerData, 'billing'),
        ]);
    }

    protected function getDefaultSelectedAddress(array $customerData, string $type = 'shipping'): null|array
    {
        switch ($type) {
            case 'shipping':
                if ( ! ($addressId = (int)$customerData['default_shipping'])) {
                    return null;
                }
                break;
            case 'billing':
                if ( ! ($addressId = (int)$customerData['default_billing'])) {
                    return null;
                }
                break;
            default:
                return null;

        }

        return current(array_filter($customerData['addresses'], static function ($address) use ($addressId) {
            return $address['id'] === $addressId;
        }));
    }
}
