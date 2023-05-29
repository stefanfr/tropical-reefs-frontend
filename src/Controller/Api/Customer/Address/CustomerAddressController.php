<?php

namespace App\Controller\Api\Customer\Address;

use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/customer/address', name: 'api_customer_address')]
class CustomerAddressController extends AbstractController
{
    public function __construct(
        protected readonly MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService,
    ) {
    }

    #[Route('/create', name: '_save', methods: ['POST'])]
    public function saveAddress(Request $request): JsonResponse
    {
        $errors = $this->magentoCustomerAddressMutationService->saveAddress($request->get('customerAddress'));

        if ( ! $errors) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    #[Route('/update/{addressId}', name: '_update', methods: ['PUT'])]
    public function updateAddress(Request $request, int $addressId): JsonResponse
    {
        $errors = $this->magentoCustomerAddressMutationService->saveAddress(
            $request->get('customerAddress'),
            $addressId
        );

        if ( ! $errors) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}