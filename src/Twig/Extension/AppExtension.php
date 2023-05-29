<?php

namespace App\Twig\Extension;

use App\Manager\Customer\CustomerSessionManager;
use App\Service\Api\Magento\Core\MagentoCoreCmsBlockService;
use App\Service\Api\Magento\Customer\Account\Wishlist\MagentoCustomerWishlistQueryService;
use App\Service\ImgProxy\ImgProxyService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        protected readonly RequestStack                        $requestStack,
        protected readonly ImgProxyService                     $imgProxyService,
        protected readonly CustomerSessionManager              $customerSessionManager,
        protected readonly MagentoCoreCmsBlockService          $magentoCoreCmsBlockService,
        protected readonly MagentoCustomerWishlistQueryService $magentoCustomerWishlistQuery,
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cmsBlock', $this->cmsBlock(...)),
            new TwigFunction('isLoggedIn', $this->isLoggedIn(...)),
            new TwigFunction('wishlistHeader', $this->wishlistHeader(...)),
            new TwigFunction('checkoutCartItemCount', $this->checkoutCartItemCount(...)),
            new TwigFunction('catalogProductFilterUrl', $this->catalogProductFilterUrl(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('cdn', [$this, 'cdn']),
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function cmsBlock(string $identifier): string
    {
        return $this->magentoCoreCmsBlockService->collectCmsBlock($identifier) ?? '';
    }

    public function isLoggedIn(): bool
    {
        return $this->customerSessionManager->isLoggedIn();
    }

    public function checkoutCartItemCount(): int
    {
        try {
            $session = $this->requestStack->getSession();

            return $session->get('checkout_cart_item_count') ?? 0;
        } catch (SessionNotFoundException $exception) {
            return 0;
        }
    }

    public function wishlistHeader(): array
    {
        $session = $this->requestStack->getSession();
        if ($session->has('customerToken')) {
            return [
                ...$this->magentoCustomerWishlistQuery->collectCustomerWishlist(true),
            ];
        }

        return [
            'items_count' => $session->get('wishlistItemCount', 0),
            'items' => $session->get('wishlistItems', []),
        ];
    }

    public function cdn(?string $uri, string $method, int|string $width, ?int $height = null): string
    {
        return $this->imgProxyService->getCdnUrl($uri, $method, $width, $height);
    }

    public function catalogProductFilterUrl(string $attributeCode, string $attributeValue): string
    {
        $session = $this->requestStack->getSession();
        $activeFilters = $session->get('activeFilters', []);
        $pathInfo = $this->requestStack->getMainRequest()?->getPathInfo();
        $pathInfo = preg_replace('/\/[a-zA-Z0-9\_]*:[a-zA-Z0-9+-]*/', '', $pathInfo);

        if (!in_array($attributeValue, $activeFilters[$attributeCode] ?? [], true)) {
            $activeFilters[urlencode($attributeCode)][] = $attributeValue;
        } else {
            $activeFilters[urlencode($attributeCode)] = array_filter(
                $activeFilters[urlencode($attributeCode)],
                static function ($value) use ($attributeValue) {
                    return $value !== $attributeValue;
                }
            );

            if (empty($activeFilters[urlencode($attributeCode)])) {
                unset($activeFilters[urlencode($attributeCode)]);
            }
        }

        $activeFilters = array_map(static function ($filter) use ($attributeValue) {
            foreach ($filter as &$value) {
                $value = urlencode($value);
            }
            unset($value);

            return implode(',', $filter);
        }, $activeFilters);

        foreach ($activeFilters as $key => $filter) {
            $pathInfo .= "/{$key}:{$filter}";
        }

        return $pathInfo;
    }
}