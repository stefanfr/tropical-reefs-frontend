<?php

namespace App\Service\GraphQL;

use App\Service\Api\Magento\Http\MageGraphQlClient;
use JsonException;

class Request
{
    public function __construct(
        protected Query             $query,
        protected MageGraphQlClient $mageGraphQlClient
    )
    {
    }

    /**
     * @throws JsonException
     */
    public function send(): array
    {
        return json_decode(
            $this->mageGraphQlClient->send(
                $this->mageGraphQlClient->post(
                    $this->mageGraphQlClient->getApiUrl('categories'),
                    json_encode(['query' => (string)$this->query], JSON_THROW_ON_ERROR)
                ),
                [
                    'headers' => ['Content-Type' => 'application/json'],
                ]
            )->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}