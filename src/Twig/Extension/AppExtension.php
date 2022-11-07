<?php

namespace App\Twig\Extension;

use App\Service\Imaginary\Http\ImaginaryHttpClient;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(
        protected ImaginaryHttpClient $imaginaryHttpClient,
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
        if ( ! $this->imaginaryHttpClient->isEnabled()) {
            return $uri;
        }

        return $this->imaginaryHttpClient->getCdnUrl($uri, $method, $width, $height);
    }
}