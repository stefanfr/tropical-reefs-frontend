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
        $query = [
            'url' => $uri,
            'width' => $width,
            'height' => $height,
        ];

        if ( ! preg_match('|^http(s)?://[a-z\d-]+(.[a-z\d-]+)*(:\d+)?(/.*)?$|i', $query['url'])) {
            $query['url'] = $this->imaginaryHttpClient->getAppUrl() . $uri;
        }

        if ($height === -1) {
            unset($query['height']);
        }

        if (is_string($width)) {
            if ($width !== 'square') {
                return $uri;
            }

            $query['width'] = $height;
        }

        $response = $this->imaginaryHttpClient->send(
            $this->imaginaryHttpClient->get(
                $method
            ),
            [
                'query' => array_merge(
                    [
                        'quality' => 90,
                        'extend' => 'white',
                        'stripmeta' => true,
                        'colorspace' => 'srgb',
                        'noprofile' => true,
                    ],
                    $query
                ),
            ]
        );

        return 'data:image/png;base64,' . base64_encode($response->getBody()->getContents());
    }
}