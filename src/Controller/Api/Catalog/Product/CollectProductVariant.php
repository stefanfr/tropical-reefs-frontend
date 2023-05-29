<?php

namespace App\Controller\Api\Catalog\Product;

use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/catalog', name: 'api_catalog_product')]
class CollectProductVariant extends AbstractController
{
    public function __construct(
        protected readonly MagentoCatalogProductApiService $magentoCatalogProductApiService,
    ) {
    }

    #[Route('/collect/product/variant', name: '_collect_variant', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $product = $this->magentoCatalogProductApiService->collectProduct(
            $request->get('productUid'),
            [$request->get('uid')]
        );

        return new JsonResponse($product['configurable_product_options_selection']);
    }
}