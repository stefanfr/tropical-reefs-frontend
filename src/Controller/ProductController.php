<?php

namespace App\Controller;

use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        protected MagentoCatalogProductApiService $magentoCatalogProductApiService
    )
    {
    }

    public function index(array $magentoMatch): Response
    {
        return $this->render('catalog/product/index.html.twig', [
            'product' => $this->magentoCatalogProductApiService->collectProduct($magentoMatch['entity_uid'], false)
        ]);
    }
}
