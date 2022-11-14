<?php

namespace App\Controller;

use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use App\Service\Api\Magento\Core\MagentoCodeStoreConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function __construct(
        protected MagentoCodeStoreConfigService    $magentoCodeStoreConfigService,
        protected MagentoCatalogCategoryApiService $magentoCatalogCategoryApiService,
    )
    {
    }

    public function index(Request $request, array $magentoMatch): Response
    {
        $options = [];
        $filters = [];
        $options = [
            'search' => '',
            'pageSize' => $this->magentoCodeStoreConfigService->getStoreConfigData('grid_per_page'),
            'currentPage' => 1,
            'sort' => [
                'value' => 'catalog_default_sort_by',
                'direction' => 'ASC',
            ],
        ];

//        $filters = [
//            [
//                'value' => 4,
//                'operator' => 'eq', // Either eq or neq
//                'attribute' => 'size',
//            ]
//        ];

//        dd($this->magentoCatalogCategoryApiService->collectCategoryProducts($magentoMatch['entity_uid'], $options, $filters));

        return $this->render('catalog/category/index.html.twig', [
            'category' => $this->magentoCatalogCategoryApiService->collectCategory($magentoMatch['entity_uid']),
            'catalog' => $this->magentoCatalogCategoryApiService->collectCategoryProducts($magentoMatch['entity_uid'], $options, $filters),
            'perPageOptions' => explode(',', $this->magentoCodeStoreConfigService->getStoreConfigData('grid_per_page_values')),
        ]);
    }
}
