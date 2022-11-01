<?php

namespace App\Service\Api\Magento\Catalog;

use App\Service\Api\Magento\Http\MageGraphQlClient;
use App\Service\Api\Magento\Http\MageRestClient;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Filter;
use App\Service\GraphQL\Filters;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Query;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class MagentoCatalogCategoryApiService
{
    public function __construct(
        protected MageRestClient    $mageRestClient,
        protected MageGraphQlClient $mageGraphQlClient
    )
    {
    }

    public function collectCategoryTree(): array
    {
        $query = (new Query('categories')
        )->addParameter(
            (new Filters)
                ->addFilter(
                    (new Filter('parent_id'))
                        ->addOperator(
                            'in',
                            ['2']
                        ),
                ),
        )->addParameter(
            new Parameter('pageSize', 20),
        )->addParameter(
            new Parameter('currentPage', 1)
        )->addFields(
            [
                new Field('total_count'),
                (new Field('items')
                )->addChildFields(
                    [
                        new Field('name'),
                        new Field('url_path'),
                        (new Field('children')
                        )->addChildFields(
                            [
                                new Field('name'),
                                new Field('url_path'),
                            ]
                        ),

                    ]
                ),
                (new Field('page_info')
                )->addChildFields(
                    [
                        new Field('current_page'),
                        new Field('page_size'),
                        new Field('total_pages'),
                    ]
                ),
            ]
        );

        try {
            $response = json_decode(
                $this->mageGraphQlClient->send(
                    $this->mageGraphQlClient->post(
                        $this->mageGraphQlClient->getApiUrl('categories'),
                        json_encode(['query' => (string)$query], JSON_THROW_ON_ERROR)
                    ),
                    [
                        'headers' => ['Content-Type' => 'application/json'],
                    ]
                )->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $response['data']['categories']['items'];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function collectCategory(int $categoryId)
    {
        return json_decode(
            $this->mageRestClient->send(
                $this->mageRestClient->get(
                    $this->mageRestClient->getApiUrl(
                        sprintf(
                            'categories/%d',
                            $categoryId
                        )
                    )
                )
            )->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}