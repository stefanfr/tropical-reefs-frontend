<?php

namespace App\Controller;

use App\Service\Api\Magento\Catalog\MagentoCatalogCategoryApiService;
use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        protected MagentoCatalogProductApiService $magentoCatalogProductApiService,
        protected MagentoCatalogCategoryApiService $magentoCatalogCategoryApiService,
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig',
            [
                'brands' => $this->getHomeBrands(),
                'categories' => $this->getHomeCategories(),
                'featuredProducts' => $this->magentoCatalogProductApiService->collectHomeFeaturedProducts(),
            ]
        );
    }

    private function getHomeCategories(): array
    {
        $homepageCategories = $this->magentoCatalogCategoryApiService->collectHomeCategories();
        usort($homepageCategories, static function ($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return ($a['homepage_position'] < $b['homepage_position']) ? -1 : 1;
        });

        return $homepageCategories;
    }

    private function getHomeBrands()
    {
        $homepageBrands = $this->magentoCatalogCategoryApiService->collectHomeBrandCategories();
        usort($homepageBrands, static function ($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return ($a['homepage_position'] < $b['homepage_position']) ? -1 : 1;
        });

        return $homepageBrands;
    }
}
