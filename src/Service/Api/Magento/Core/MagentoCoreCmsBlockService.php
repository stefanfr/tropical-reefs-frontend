<?php

namespace App\Service\Api\Magento\Core;

use App\Cache\Adapter\RedisAdapter;
use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use App\Service\GraphQL\Request;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;

class MagentoCoreCmsBlockService
{
    public function __construct(
        protected MageGraphQlClient $mageGraphQlClient,
        protected RedisAdapter      $redisAdapter,
    )
    {
    }

    /**
     * @param string $identifier
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function collectCmsBlock(string $identifier): ?string
    {
        $cacheKey = 'magento_cms_block_' . $identifier;

        return $this->redisAdapter->get($cacheKey, function (ItemInterface $item) use ($identifier) {
            $item->expiresAfter(24 * 60 * 60);

            try {
                $response = (new Request(
                    (new Query('cmsBlocks'))
                        ->addParameter(
                            new Parameter('identifiers', $identifier)
                        )->addField(
                            (new Field('items')
                            )->addChildFields(
                                [
                                    new Field('content'),
                                ]
                            )
                        ),
                    $this->mageGraphQlClient
                ))->send();

                return preg_replace("~<style(.*)>(.*)</style>~", " ", current($response['data']['cmsBlocks']['items'])['content'] ?? '');
            } catch (\JsonException $e) {
                return null;
            }
        });
    }
}