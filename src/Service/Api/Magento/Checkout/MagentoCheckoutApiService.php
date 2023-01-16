<?php

namespace App\Service\Api\Magento\Checkout;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
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
        $session = null;
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException $exception) {
        }

        if ($fresh || ! $session?->has('checkout_quote_mask')) {
            $response = (new Request(
                new Mutation(
                    'createEmptyCart'
                ),
                $this->mageGraphQlClient
            ))->send();

            if ( ! ($response['data']['createEmptyCart'])) {
                throw new BadRequestException('Unable to create cart');
            }

            $session?->set('checkout_quote_mask', $response['data']['createEmptyCart']);
        }

        return $session?->get('checkout_quote_mask');
    }

    public function collectCountries(): array
    {
        return $this->redisAdapter->get('checkout_shipping_countries', function () {
            $response = (new Request(
                (new Query('countries')
                )->addFields(
                    [
                        new Field('id'),
                        new Field('two_letter_abbreviation'),
                        new Field('full_name_locale'),
                        new Field('full_name_english'),
                        (new Field('available_regions')
                        )->addChildFields(
                            [
                                new Field('id'),
                                new Field('code'),
                                new Field('name'),
                            ]
                        ),
                    ]
                ),
                $this->mageGraphQlClient
            ))->send();

            return $response['data']['countries'] ?? throw new BAdRequestException('Could not collect countries');
        });
    }
}