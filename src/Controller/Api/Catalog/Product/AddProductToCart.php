<?php

namespace App\Controller\Api\Catalog\Product;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog', name: 'api_catalog_product')]
class AddProductToCart extends AbstractController
{
    public function __construct(
        protected readonly MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
    ) {
    }

    #[Route('/add/product', name: '_add_product', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $response = $this->magentoCheckoutCartApiService->addProductToCart(
            $request->request->all(),
        );

        return new JsonResponse(
            $response,
            empty($response['user_errors']) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}