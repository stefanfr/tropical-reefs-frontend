<?php

namespace App\Service\Postcode;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Postcode\PostcodeApiService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;

class PostcodeService
{
    public function __construct(
        protected RedisAdapter       $redisAdapter,
        protected RequestStack       $requestStack,
        protected PostcodeApiService $postcodeApiService,
    )
    {
    }

    /**
     * @param string $postcode
     * @param string $houseNr
     * @param string|null $houseNrAdd
     * @return array
     * @throws InvalidArgumentException
     */
    public function lookup(string $postcode, string $houseNr, string $houseNrAdd = null): array
    {
        return $this->redisAdapter->get(
            'postcode-lookup-' . implode('-', [$postcode, $houseNr, $houseNrAdd]),
            function (ItemInterface $item) use ($postcode, $houseNr, $houseNrAdd) {
                $item->expiresAfter(7 * 24 * 60 * 60);

                $uri = sprintf(
                    'api/getAddress/%s/%s%s',
                    $postcode,
                    $houseNr,
                    $houseNrAdd
                );

                $response = $this->postcodeApiService->send(
                    $this->postcodeApiService->get(
                        $uri
                    )
                );

                return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            }
        );
    }
}