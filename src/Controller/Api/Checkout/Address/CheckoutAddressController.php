<?php

namespace App\Controller\Api\Checkout\Address;

use App\Service\Api\Magento\Checkout\MagentoCheckoutAddressApiService;
use App\Service\Postcode\PostcodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/checkout/address', name: 'api_checkout_address')]
class CheckoutAddressController extends AbstractController
{

    public function __construct(
        protected readonly PostcodeService $postcodeService,
        protected readonly MagentoCheckoutAddressApiService $magentoCheckoutAddressApiService,
    ) {
    }

    #[Route('/customer-email', name: '_customer_email', methods: ['POST'])]
    public function saveCustomerEmail(Request $request): JsonResponse
    {
        $errors = $this->magentoCheckoutAddressApiService->setCustomerEmail($request->get('customer_email'));

        if ( ! $errors) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    #[Route('/save/{type}', name: '_save', methods: ['POST'])]
    public function index(Request $request, string $type): RedirectResponse|JsonResponse
    {
        $addressId = $request->get('addressId');
        $address = $request->get('address');
        $sameAsShippingAddress = $request->get('sameAsShippingAddress');
//        $this->magentoCheckoutAddressApiService->setCustomerEmail($this->address->getCustomerEmail());


        if (null !== $addressId) {
            $errors = match ($type) {
                'shipping' => $this->magentoCheckoutAddressApiService->saveShippingAddressId($addressId),
                'billing' => $this->magentoCheckoutAddressApiService->saveBillingAddressId(
                    $addressId,
                    $sameAsShippingAddress
                ),
            };
        } else {
            $errors = match ($type) {
                'shipping' => $this->magentoCheckoutAddressApiService->saveShippingAddressDetails($address),
                'billing' => $this->magentoCheckoutAddressApiService->saveBillingAddressDetails(
                    $address,
                    $sameAsShippingAddress
                ),
            };
        }

        if ( ! $errors) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Successfully saved address',
                ]
            );
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}