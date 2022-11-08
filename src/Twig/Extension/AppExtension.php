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
        $filters = match ($method) {
            'resize' => 'resize:fill:',
            'crop' => 'crop:',
            default => 'plain',
        };

        $filters .= match ($width) {
            'square' => sprintf('%s:%s', $height, $height),
            default => sprintf('%s:%s', $width, $height),
        };

        $filters .= '/gravity:sm';

        return $this->imgProxyService->getUrl(
            $uri,
            $filters
        );
    }
}