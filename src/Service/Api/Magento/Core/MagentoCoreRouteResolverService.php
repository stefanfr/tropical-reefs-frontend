<?php

namespace App\Service\Api\Magento\Core;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use JsonException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCoreRouteResolverService
{
    public function __construct(
        protected RequestStack      $requestStack,
        protected RedisAdapter      $redisAdapter,
        protected MageGraphQlClient $mageGraphQlClient,
    )
    {
    }

    public function resolverRoute(string $pathInfo): false|array
    {
        if (str_contains($pathInfo, '_components')) {
            return false;
        }

        preg_match_all('/\/[a-zA-Z0-9\_]*:[a-zA-Z0-9+-]*/', $pathInfo, $filters);
        $pathInfo = preg_replace('/\/[a-zA-Z0-9\_]*:[a-zA-Z0-9+-]*/', '', $pathInfo);

        $this->prepareFilters($filters);

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
                } catch (JsonException $e) {
                    return false;
                }
            }
        );
    }

    protected function prepareFilters(array $filters = []): void
    {
        $session = $this->requestStack->getSession();
        $session->set('activeFilters', []);
        $activeFilters = [];

        foreach (current($filters ?? []) as $filter) {
            $_filter = explode(':', $filter);
            if (count($_filter) <= 1) {
                continue;
            }
            $_key = str_replace('/', '', $_filter[0]);
            unset($_filter[0]);
            $_filter = array_values($_filter);

            foreach ($_filter as &$value) {
                $value = urldecode($value);
            }
            unset($value);

            $activeFilters[$_key] = $_filter;
        }

        $session->set('activeFilters', $activeFilters);
    }
}