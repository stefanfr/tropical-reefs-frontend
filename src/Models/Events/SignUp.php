<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SignUpContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class SignUp extends AbstractEventModel implements SignUpContract
{
    protected static string $eventName = 'sign_up';

    public function __construct(
        protected readonly ?string $method = 'generic',
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
                    'method' => $this->method,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'method' => $data['method'] ?? null,
        ];
    }
}