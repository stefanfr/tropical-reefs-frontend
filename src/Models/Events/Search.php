<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SearchContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class Search extends AbstractEventModel implements SearchContract
{
    protected static string $eventName = 'search';

    public function __construct(
        protected readonly string $searchTerm,
    )
    {
    }

    public function toGABody(): array
    {
        return [
            'name' => static::eventName(),
            'params' => array_filter(
                [
                    'debug_mode' => 1,
                    'session_id' => MeasurementProtocolService::getSessionId(),
                    'search_term' => $this->searchTerm,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'searchTerm' => $data['search_term'],
        ];
    }
}