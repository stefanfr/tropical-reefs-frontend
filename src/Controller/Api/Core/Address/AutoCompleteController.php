<?php

namespace App\Controller\Api\Core\Address;

use App\Service\Postcode\PostcodeService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/address', name: 'api_core_address')]
class AutoCompleteController extends AbstractController
{

    public function __construct(
        protected readonly PostcodeService $postcodeService,
    ) {
    }

    #[Route('/autocomplete', name: '_autocomplete', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $address = $request->get('address');

        try {
            $lookupDetails = $this->postcodeService->lookup(
                $address['postcode'],
                $address['houseNr'],
                $address['houseNrAdd'] ?? ''
            );

            if (array_key_exists('error', $lookupDetails)) {
                return new JsonResponse($lookupDetails, 404);
            }
        } catch (InvalidArgumentException $e) {
        }

        return new JsonResponse($lookupDetails);
    }
}