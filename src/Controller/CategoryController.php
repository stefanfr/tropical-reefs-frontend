<?php

namespace App\Controller;

use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        protected MagentoCatalogCategoryApiService $magentoCatalogCategoryApiService
    )
    {
    }

    public function index(array $magentoMatch): Response
    {
        return $this->render('catalog/category/index.html.twig', [
            'catalog' => $this->magentoCatalogCategoryApiService->collectCategory($magentoMatch['entity_uid']),
            'subCategories' => $this->magentoCatalogCategoryApiService->collectCategoryTree($magentoMatch['entity_uid'])
        ]);
    }
}
