<?php

namespace App\Routing;

use App\Service\Api\Magento\Core\MagentoCoreRouteResolverService;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

class MagentoRouteMatcher implements UrlMatcherInterface
{
    protected string $filterString;
    protected ?array $rawFilters = null;
    protected string $filterDelimiter = '_';

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

        return match ($magentoMatch['type']) {
            'CATEGORY' => $this->resolverCategoryRoute($magentoMatch),
            'PRODUCT' => $this->resolverProductRoute($magentoMatch),
            'CMS_PAGE' => $this->resolverCmsPageRoute($magentoMatch),
            default => [],
        };

    }

    protected function resolverCategoryRoute(array $magentoMatch): array
    {
        return [
            '_controller' => 'App\Controller\Catalog\CategoryController::index',
            'magentoMatch' => $magentoMatch,
        ];
    }

    protected function resolverProductRoute(array $magentoMatch): array
    {
        return [
            '_controller' => 'App\Controller\Catalog\ProductController::index',
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

    /**
     * Resolve the filters from the request.
     *
     * @return void
     */
    protected function resolveFilters()
    {

    }

    /**
     * @return array|null
     */
    protected function getRawFilters(): ?array
    {
        if (null === $this->rawFilters) {
            $this->rawFilters = explode(
                $this->filterDelimiter,
                $this->filterString
            );

            $this->rawFilters = array_unique(
                array_filter($this->rawFilters)
            );
        }

        return $this->rawFilters;
    }

    /**
     * @param string $pathInfo
     * @return bool
     */
    protected function routeHasAttributeFilters(string $pathInfo): bool
    {
        static $hasFilters;

        if (null === $hasFilters) {
            $hasFilters = preg_match('~/_([^?/]+)~', $pathInfo, $matches) && is_string($this->filterString = $matches[1]);
        }

        return $hasFilters;
    }
}