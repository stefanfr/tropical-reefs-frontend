<?php

namespace App\Controller\Catalog;

use App\Service\Api\Magento\Catalog\MagentoCatalogAttributeApiService;
use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use App\Service\Api\Magento\Core\MagentoCoreStoreConfigService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public function __construct(
        protected readonly RequestStack                      $requestStack,
        protected readonly MagentoCoreStoreConfigService     $magentoCoreStoreConfigService,
        protected readonly MagentoCatalogCategoryApiService  $magentoCatalogCategoryApiService,
        protected readonly MagentoCatalogAttributeApiService $magentoCatalogAttributeApiService,
    )
    {
    }

    public function index(Request $request, array $magentoMatch): Response
    {
        $options = [];
        $filters = [];
        $options = [
            'search' => '',
            'pageSize' => $this->magentoCoreStoreConfigService->getStoreConfigData('grid_per_page'),
            'currentPage' => $request->get('page', 1),
//            'sort' => [
//                'value' => 'catalog_default_sort_by',
//                'direction' => 'ASC',
//            ],
        ];

        $filters = [];
        foreach (($activeFilters = $request->getSession()->get('activeFilters')) ?? [] as $key => $filter) {
            $filters[] = $this->prepareFilter(
                [
                    'value' => $filter,
                    'operator' => 'in',
                    'attribute' => $key,
                ]
            );
        }

        $catalog = $this->magentoCatalogCategoryApiService->collectCategoryProducts($magentoMatch['entity_uid'], $options, $filters);
        $category = $this->magentoCatalogCategoryApiService->collectCategory($magentoMatch['entity_uid']);

        return $this->render('catalog/category/index.html.twig', [
            'activeFilters' => $activeFilters,
            'pagination' => $this->getPagination($catalog, $magentoMatch),
            'category' => $category,
            'catalog' => $catalog,
            'breadcrumbs' => $this->generateBreadcrumbs($category),
            'perPageOptions' => explode(',', $this->magentoCoreStoreConfigService->getStoreConfigData('grid_per_page_values')),
        ]);
    }

    protected function generateBreadcrumbs(array $category): array
    {
        $breadcrumbs = [];
        foreach ($category['breadcrumbs'] ?? [] as $_category) {
            $breadcrumbs[] = [
                'label' => $_category['category_name'],
                'url' => $_category['category_url_path'],
            ];
        }

        $breadcrumbs[] = [
            'active' => true,
            'label' => $category['name'],
            'url' => $category['url_path'],
        ];

        try {
            $session = $this->requestStack->getSession();
            $session->set('breadcrumbs', $breadcrumbs);
        } catch (SessionNotFoundException $exception) {
        }

        return $breadcrumbs;
    }

    protected function getPagination(array $catalog, array $magentoMatch, array $attributes = []): array
    {
        $maxPages = 4;
        $pages = $catalog['page_info'];
        $currentPage = $pages['current_page'];
        $totalPages = $pages['total_pages'];
        $displayPages = [];
        $maxLeftCount = ($currentPage + 1 - ($maxPages / 2));
        $maxRightCount = ($totalPages - $currentPage);

        if ($maxLeftCount > $maxPages / 2) {
            $maxLeftCount = ($maxRightCount < $maxPages / 2) ? ($maxPages - $maxRightCount) : $maxPages / 2;
        }

        for ($i = $maxLeftCount; $i > 0; $i--) {
            $displayPages[] = [
                'url' => $this->getPaginationUrl($currentPage - $i, $magentoMatch['relative_url'], $attributes),
                'value' => $currentPage - $i,
            ];
        }
        $maxPages -= count($displayPages);

        if ($maxRightCount > $maxPages) {
            $maxRightCount = $maxPages;
        }

        $displayPages[] = [
            'url' => $this->getPaginationUrl($currentPage, $magentoMatch['relative_url'], $attributes),
            'value' => $currentPage,
        ];

        for ($i = 0; $i < $maxRightCount; $i++) {
            $displayPages[] = [
                'url' => $this->getPaginationUrl($currentPage + ($i + 1), $magentoMatch['relative_url'], $attributes),
                'value' => $currentPage + ($i + 1),
            ];
        }

        return [
            'nextPage' => $this->getPaginationUrl($currentPage + 1, $magentoMatch['relative_url'], $attributes),
            'displayPages' => $displayPages,
            'prevPage' => $currentPage === 1 ? null : $this->getPaginationUrl($currentPage - 1, $magentoMatch['relative_url'], $attributes),
        ];
    }

    protected function getPaginationUrl(int $pageNr, string $relativeUrl, array $urlAttributes): string
    {
        $queryString = http_build_query(
            array_merge(
                [
                    'page' => $pageNr,
                ],
                $urlAttributes
            )
        );

        return '/' . $relativeUrl . '?' . $queryString;
    }

    protected function prepareFilter(array $filter): array
    {
        static $attributeMetaData;

        if ( ! isset($attributeMetaData[$filter['attribute']])) {
            try {
                $attributeMetaData[$filter['attribute']] = $this->magentoCatalogAttributeApiService->collectAttributeMetaData($filter['attribute']);
            } catch (InvalidArgumentException $e) {
                $attributeMetaData[$filter['attribute']] = [];
            }
        }

        $attributeValue = array_values(
            array_filter($attributeMetaData[$filter['attribute']]['attribute_options'] ?? [], static function ($value) use ($filter) {
                return in_array($value['label'], $filter['value'], true);
            })
        );

        $filter['value'] = array_map(
            static function ($attributeValue) {
                return $attributeValue['value'];
            },
            $attributeValue
        );

        return $filter;
    }

}
