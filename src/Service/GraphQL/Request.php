<?php

namespace App\Service\GraphQL;

use App\Service\Api\Magento\Http\MageGraphQlClient;
use Exception;

class Request
{
    public function __construct(
        protected Query|Mutation    $query,
        protected MageGraphQlClient $mageGraphQlClient,
    )
    {
    }

    /**
     */
    public function send(): array
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
            ];

            $response = $this->mageGraphQlClient->send(
                $this->mageGraphQlClient->post(
                    $this->mageGraphQlClient->getApiUrl(),
                    json_encode(['query' => (string)$this->query], JSON_THROW_ON_ERROR)
                ),
                [
                    'headers' => $headers,
                ]
            )->getBody()->getContents();

            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            echo (string)$this->query;
            dd($exception->getMessage());
        }
    }
}