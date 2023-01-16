<?php

namespace App\Service\Api\Magento;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class BaseMagentoService
{
    public function __construct(
        protected MageGraphQlClient $mageGraphQlClient,
        protected RedisAdapter      $redisAdapter,
        protected RequestStack      $requestStack,
    )
    {
    }

    protected function checkErrorsResponse(array $response, string $target = 'default'): array
    {
        if (isset($response['errors'])) {
            foreach ($response['errors'] as $error) {
                if (($error['extensions']['category'] ?? null) === 'graphql-authorization') {
                    try {
                        $session = $this->requestStack->getSession();
                        switch ($target) {
                            case 'customer':
                                $session->remove('customerToken');
                                break;
                            default:
                                $session->clear();
                                break;
                        }
                    } catch (SessionNotFoundException $exception) {

                    }
                }
            }
        }

        return $response;
    }
}