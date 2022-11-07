<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;

class MagentoCheckoutApiService
{
    public function __construct(
        protected RedisAdapter      $redisAdapter,
        protected RequestStack      $requestStack,
        protected MageGraphQlClient $mageGraphQlClient,
    )
    {
    }

    public function getQuoteMaskId(bool $fresh = false): string
    {
        $session = $this->requestStack->getSession();

        if ($fresh || ! $session->has('checkout_quote_mask')) {
            $response = (new Request(
                new Mutation(
                    'createEmptyCart'
                ),
                $this->mageGraphQlClient
            ))->send();

            if ( ! ($quoteMask = $response['data']['createEmptyCart'])) {
                throw new BadRequestException('Unable to create cart');
            }

            $session->set('checkout_quote_mask', $response['data']['createEmptyCart']);
        }

        return $session->get('checkout_quote_mask');
    }
}