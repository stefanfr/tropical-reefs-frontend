<?php

namespace App\Controller\Checkout;

use App\DataClass\Checkout\Address\Address;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractCheckoutController
{
    #[Route('/checkout/address', name: 'app_checkout_address')]
    public function index(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();

        $cart = $this->magentoCheckoutCartApiService->collectFullCart();
        $shippingAddress = new Address();
        $billingAddress = new Address();

//        if ( ! $cart['total_quantity']) {
//            return $this->redirectToRoute('app_checkout_cart');
//        }

        if (isset($cart['shipping_addresses'][0])) {
            $shippingAddress = $this->convertAddress($shippingAddress, $cart['shipping_addresses'][0]);
        }

        if (null !== $cart['billing_address']) {
            $billingAddress = $this->convertAddress($billingAddress, $cart['billing_address']);
        }

        return $this->render('checkout/checkout/address.html.twig', [
            'cart' => $cart,
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
            'isLoggedIn' => $session->has('customerToken'),
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
}
