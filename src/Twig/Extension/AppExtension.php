<?php

namespace App\Twig\Extension;

use App\Service\Api\Magento\Core\MagentoCoreCmsBlockService;
use App\Service\ImgProxy\ImgProxyService;
use Psr\Cache\InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        protected ImgProxyService            $imgProxyService,
        protected MagentoCoreCmsBlockService $magentoCoreCmsBlockService,
    )
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cmsBlock', $this->cmsBlock(...)),
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

    public function cdn(?string $uri, string $method, int|string $width, ?int $height = null): string
    {
        if (null === $uri) {
            $uri = match ($width) {
                'square' => sprintf('https://via.placeholder.com/%s', $height),
                default => sprintf('https://via.placeholder.com/%sx%s', $width, $height),
            };
        }

        $uri = str_replace('aquastore.test', 'dev.tropicalreefs.nl', $uri);
        if ( ! filter_var($uri, FILTER_VALIDATE_URL)) {
            $uri = 'https://dev.tropicalreefs.nl/' . $uri;
        }

        $filters = 'pr:sharp/';

        $filters .= match ($method) {
            'crop' => 'crop:',
            'fit' => 'rs:fit:',
            'fill' => 'rs:fill:',
            'fill-down' => 'rs:fill-down:',
            'force' => 'rs:force:',
            'auto' => 'rs:auto:',
            default => 'plain',
        };

        $filters .= match ($width) {
            'square' => sprintf('%s:%s', $height, $height),
            default => sprintf('%s:%s', $width, $height),
        };

        $filters .= '/ex:1';
        $filters .= '/g:ce';
        $filters .= '/ar:1';

        return $this->imgProxyService->getUrl(
            preg_replace('/cache\/.*\//U', '', $uri),
            $filters
        );
    }
}