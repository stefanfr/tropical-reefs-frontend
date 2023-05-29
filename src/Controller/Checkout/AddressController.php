<?php

namespace App\Controller\Checkout;

use App\DataClass\Checkout\Address\Address;
use App\Manager\Customer\CustomerSessionManager;
use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressQueryService;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractCheckoutController
{
    public function __construct(
        MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
        protected readonly RequestStack $requestStack,
        protected readonly CustomerSessionManager $customerSessionManager,
        protected readonly MagentoCustomerAddressQueryService $magentoCustomerAddressQueryService,
    ) {
        parent::__construct($magentoCheckoutCartApiService);
    }

    #[Route('/checkout/address', name: 'app_checkout_address')]
    public function index(RequestStack $requestStack): Response
    {
        $cart = $this->magentoCheckoutCartApiService->collectFullCart();
        $shippingAddress = new Address();
        $billingAddress = new Address();

        $billingAddress->setCustomerEmail($cart['email']);

        if ( ! $cart['total_quantity']) {
            return $this->redirectToRoute('app_checkout_cart');
        }

        if (isset($cart['shipping_addresses'][0])) {
            $shippingAddress = $this->convertAddress($shippingAddress, $cart['shipping_addresses'][0]);
        }

        if (null !== $cart['billing_address']) {
            $billingAddress = $this->convertAddress($billingAddress, $cart['billing_address']);
        }

        return $this->render('checkout/checkout/address.html.twig', [
            'cart' => $cart,
            'customerAddresses' => $this->collectAddresses(),
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'isLoggedIn' => $this->customerSessionManager->isLoggedIn(),
        ]);
    }

    protected function convertAddress(Address $address, array $quoteAddress): Address
    {
        $street = $quoteAddress['street'];
        $houseNrParts = explode(' ', $street[1]);

        return $address
            ->setId($quoteAddress['id'] ?? null)
            ->setFirstname($quoteAddress['firstname'])
            ->setLastname($quoteAddress['lastname'])
            ->setStreet($street[0])
            ->setHouseNr($houseNrParts[0])
            ->setAdd($houseNrParts[1] ?? null)
            ->setCity($quoteAddress['city'])
            ->setPostcode($quoteAddress['postcode'])
            ->setPhone($quoteAddress['telephone'])
            ->setCountryCode($quoteAddress['country']['code'] ?? 'NL');
    }

    public function collectAddresses(): array
    {
        static $addresses;

        if (null !== $addresses) {
            return $addresses;
        }

        $addresses = [];

        try {
            $session = $this->requestStack->getSession();
            if ($session->has('customerToken')) {
                $addresses = $this->magentoCustomerAddressQueryService->collectCustomerAddresses();
            }
        } catch (SessionNotFoundException $exception) {
        }

        return $addresses;
    }
}
