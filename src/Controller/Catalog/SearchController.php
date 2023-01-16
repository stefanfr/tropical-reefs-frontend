<?php

namespace App\Controller\Catalog;

use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use App\Service\Api\Magento\Catalog\MagentoCatalogSearchApiService;
use App\Service\Api\Magento\Core\MagentoCoreStoreConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchController extends AbstractController
{
    public function __construct(
        protected RequestStack                     $requestStack,
        protected MagentoCoreStoreConfigService    $magentoCoreStoreConfigService,
        protected MagentoCatalogCategoryApiService $magentoCatalogCategoryApiService,
    )
    {
    }

    #[Route('/search', name: 'app_catalog_search')]
    public function search(TranslatorInterface $translator, Request $request): Response
    {
        $query = $request->query->get('q');
        $options = [];
        $filters = [];
        $options = [
            'search' => $query,
            'pageSize' => $this->magentoCoreStoreConfigService->getStoreConfigData('grid_per_page'),
            'currentPage' => $request->get('page', 1),
//            'sort' => [
//                'value' => 'catalog_default_sort_by',
//                'direction' => 'ASC',
//            ],
        ];

//        $filters = [
//            [
//                'value' => 28,
//                'operator' => 'eq', // Either eq or neq
//                'attribute' => 'brand',
//            ],
//        ];

        $catalog = $this->magentoCatalogCategoryApiService->collectCategoryProducts(null, $options, $filters);
        if ( ! $catalog) {
            throw $this->createNotFoundException('The product does not exist');
        }
//        $category = $this->magentoCatalogCategoryApiService->collectCategory($magentoMatch['entity_uid']);

        $relativeUrl = $request->getPathInfo();
        $attributes = [];
        parse_str($request->getQueryString(), $attributes);
        return $this->render('catalog/category/index.html.twig', [
            'pagination' => $this->getPagination(
                $catalog,
                $relativeUrl,
                $attributes
            ),
            'category' => [
                'name' => $translator->trans('Search results for: %query%', ['%query%' => $query]),
                'children' => [],
            ],
            'catalog' => $catalog,
            'breadcrumbs' => $this->generateBreadcrumbs(
                $translator->trans('Search results for: %query%', ['%query%' => $query]),
                substr(str_replace('%20', '+', implode('?', [$request->getPathInfo(), $request->getQueryString()])), 1)
            ),
            'perPageOptions' => explode(',', $this->magentoCoreStoreConfigService->getStoreConfigData('grid_per_page_values')),
        ]);
    }

    protected function generateBreadcrumbs(string $name, string $url): array
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
            'label' => $name,
            'url' => $url,
        ];

        try {
            $session = $this->requestStack->getSession();
            $session->set('breadcrumbs', $breadcrumbs);
        } catch (SessionNotFoundException $exception) {
        }

        return $breadcrumbs;
    }

    protected function getPagination(array $catalog, string $relativeUrl, array $attributes = []): array
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
                'url' => $this->getPaginationUrl($currentPage - $i, $relativeUrl, $attributes),
                'value' => $currentPage - $i,
            ];
        }
        $maxPages -= count($displayPages);

        if ($maxRightCount > $maxPages) {
            $maxRightCount = $maxPages;
        }

        $displayPages[] = [
            'url' => $this->getPaginationUrl($currentPage, $relativeUrl, $attributes),
            'value' => $currentPage,
        ];

        for ($i = 0; $i < $maxRightCount; $i++) {
            $displayPages[] = [
                'url' => $this->getPaginationUrl($currentPage + ($i + 1), $relativeUrl, $attributes),
                'value' => $currentPage + ($i + 1),
            ];
        }

        return [
            'nextPage' => $this->getPaginationUrl($currentPage + 1, $relativeUrl, $attributes),
            'displayPages' => $displayPages,
            'prevPage' => $currentPage === 1 ? null : $this->getPaginationUrl($currentPage - 1, $relativeUrl, $attributes),
        ];
    }

    protected function getPaginationUrl(int $pageNr, string $relativeUrl, array $urlAttributes): string
    {
        $queryString = http_build_query(
            array_merge(
                $urlAttributes,
                [
                    'page' => $pageNr,
                ],
            )
        );

        return $relativeUrl . '?' . $queryString;
    }
}