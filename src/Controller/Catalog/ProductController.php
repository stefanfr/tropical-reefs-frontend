<?php

namespace App\Controller\Catalog;

use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        protected RequestStack                    $requestStack,
        protected MagentoCatalogProductApiService $magentoCatalogProductApiService
    )
    {
    }

    public function index(array $magentoMatch): Response
    {
        $product = $this->magentoCatalogProductApiService->collectProduct($magentoMatch['entity_uid'], [], );

        return $this->render('catalog/product/index.html.twig', [
            'product' => $product,
            'breadcrumbs' => $this->generateBreadcrumbs($product),
        ]);
    }

    protected function generateBreadcrumbs(array $product): array
    {
        $breadcrumbs = [];
        $session = $this->requestStack->getSession();;
        foreach ($session->get('breadcrumbs', []) ?? [] as $breadcrumb) {
            $breadcrumbs[] = [
                'label' => $breadcrumb['label'],
                'url' => $breadcrumb['url'],
            ];
        }

        $breadcrumbs[] = [
            'active' => true,
            'label' => $product['name'],
        ];

        return $breadcrumbs;
    }
}
