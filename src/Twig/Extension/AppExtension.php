<?php

namespace App\Twig\Extension;

use App\Service\ImgProxy\ImgProxyService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(
        protected ImgProxyService $imgProxyService,
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('cdn', [$this, 'cdn']),
        ];
    }

    public function cdn(string $uri, string $method, int|string $width, int $height): string
    {
        $uri = str_replace('aquastore.test', 'dev.tropicalreefs.nl', $uri);
        if ( ! preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $uri)) {
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

        $filters .= '/gravity:ce';

        return $this->imgProxyService->getUrl(
            preg_replace('/cache\/.*\//U', '', $uri),
            $filters
        );
    }
}