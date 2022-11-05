<?php

namespace App\Routing;

use App\Service\Api\Magento\Core\MagentoCoreRouteResolverService;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

class MagentoRouteMatcher implements UrlMatcherInterface
{

    public function __construct(
        protected UrlMatcherInterface             $matcher,
        protected RequestContext                  $context,
        protected MagentoCoreRouteResolverService $magentoCoreRouteResolverService,
    )
    {
    }

    public function setContext(RequestContext $context)
    {
        return $this->matcher->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->matcher->getContext();
    }

    public function match(string $pathinfo): array
    {
        $magentoMatch = $this->magentoCoreRouteResolverService->resolverRoute($pathinfo);


        if ( ! $magentoMatch) {
            return $this->matcher->match($pathinfo);
        }

        if ($magentoMatch['redirectCode'] !== 0) {
            return [
                '_controller' => 'App\Controller\RedirectController::index',
                'magentoMatch' => $magentoMatch,
            ];
        }

        switch ($magentoMatch['type']) {
            case 'CATEGORY':
                return $this->resolverCategoryRoute($magentoMatch);
            case 'PRODUCT':
                return $this->resolverProductRoute($magentoMatch);
            case 'CMS_PAGE':
                return $this->resolverCmsPageRoute($magentoMatch);
        }
    }

    protected function resolverCategoryRoute(array $magentoMatch): array
    {
        return [
            '_controller' => 'App\Controller\CategoryController::index',
            'magentoMatch' => $magentoMatch,
        ];
    }

    protected function resolverProductRoute(array $magentoMatch): array
    {
        return [
            '_controller' => 'App\Controller\ProductController::index',
            'magentoMatch' => $magentoMatch,
        ];
    }

    protected function resolverCmsPageRoute(array $magentoMatch): array
    {
        return [
            '_controller' => 'App\Controller\CmsPageController::index',
            'magentoMatch' => $magentoMatch,
        ];
    }
}