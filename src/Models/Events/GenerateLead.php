<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\GenerateLeadContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class GenerateLead extends AbstractEventModel implements GenerateLeadContract
{
    protected static string $eventName = 'generate_lead';

    public function __construct(
        protected readonly float  $value,
        protected readonly string $currency = 'EUR',
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
                    'value' => $this->value,
                    'currency' => $this->currency,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'value' => $data['value'],
            'currency' => $data['currency']
        ];
    }
}