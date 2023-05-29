<?php

namespace App\Controller\Catalog;

use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use App\Service\ImgProxy\ImgProxyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly ImgProxyService $imgProxyService,
        protected readonly MagentoCatalogProductApiService $magentoCatalogProductApiService
    ) {
    }

    public function index(array $magentoMatch): Response
    {
        $product = $this->magentoCatalogProductApiService->collectProduct($magentoMatch['entity_uid'], [],);

        return $this->render('catalog/product/index.html.twig', [
            'product' => $product,
            'mediaGallery' => $this->convertMediaGallery($product['media_gallery']),
            'breadcrumbs' => $this->generateBreadcrumbs($product),
        ]);
    }

    protected function generateBreadcrumbs(array $product): array
    {
        $breadcrumbs = [];
        $session = null;
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException $exception) {
        }
        foreach ($session?->get('breadcrumbs', []) ?? [] as $breadcrumb) {
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

    protected function convertMediaGallery(array $mediaGallery): array
    {
        $imageSizes = [
            '640px' => 335,
            '678px' => 592,
            '1024px' => 414,
        ];
        $sources = [];
        usort($mediaGallery, static function (array $a, array $b): int {
            return $a['position'] > $b['position'] ? 1 : -1;
        });

        foreach ($mediaGallery as $mediaGalleryItem) {
            $imageData = [
                'url' => $this->imgProxyService->getCdnUrl($mediaGalleryItem['url'], 'auto', 'square', 592),
                'sources' => [],
            ];

            foreach ($imageSizes as $imageSize => $width) {
                $imageData['sources'][] = [
                    'size' => $imageSize,
                    'url' => $this->imgProxyService->getCdnUrl($mediaGalleryItem['url'], 'auto', 'square', $width),
                ];
            }

            $sources[] = $imageData;
        }

        return $sources;
    }
}
