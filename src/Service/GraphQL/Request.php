<?php

namespace App\Service\GraphQL;

use App\Service\Api\Magento\Http\MageGraphQlClient;
use Exception;

class Request
{
    protected ?int $statusCode;

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
            );

            $this->setStatusCode($response->getStatusCode());

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            echo (string)$this->query;
            dd($exception->getMessage());
        }
    }

    /**
     * @return int|null
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}