<?php

namespace App\Service\Api\Magento\Core;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCoreRouteResolverService
{
    public function __construct(
        protected MageGraphQlClient $mageGraphQlClient,
        protected RedisAdapter      $redisAdapter,
    )
    {
    }

    public function resolverRoute(string $pathInfo): false|array
    {
        if (str_contains($pathInfo, '_components')) {
            return false;
        }

        return $this->redisAdapter->get(
            'magento_route_resolver_' . preg_replace('/[{}()\/\@:]/m', '-', $pathInfo),
            function (ItemInterface $item) use ($pathInfo) {
                $item->expiresAfter(24 * 60 * 60);

                try {
                    $response = (new Request(
                        (new Query('urlResolver'))
                            ->addParameter(
                                (new Parameter('url', $pathInfo))
                            )->addFields(
                                [
                                    new Field('entity_uid'),
                                    new Field('relative_url'),
                                    new Field('redirectCode'),
                                    new Field('type'),
                                ]
                            ),
                        $this->mageGraphQlClient
                    ))->send();

                    return $response['data']['urlResolver'] ?? false;
                } catch (\JsonException $e) {
                    return false;
                }
            }
        );
    }
}