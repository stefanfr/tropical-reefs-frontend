<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\CustomContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class Custom extends AbstractEventModel implements CustomContract
{
    protected static string $eventName = 'custom';
    protected array $params;

    public function __construct(
        protected readonly string $_eventName,
        array                     ...$params
    )
    {
        $this->params = $params;
    }

    public function toGABody(): array
    {
        return [
            'name' => $this->_eventName,
            'params' => array_filter([
                'debug_mode' => 1,
                'session_id' => MeasurementProtocolService::getSessionId(),
            ],
                ...$this->params
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            '_eventName' => $data['event_name'],
            'params' => [
                ...$data
            ],
        ];
    }
}