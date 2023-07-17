<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\TutorialBeginContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class TutorialBegin extends AbstractEventModel implements TutorialBeginContract
{
    protected static string $eventName = 'tutorial_begin';

    public function __construct()
    {
    }

    public function toGABody(): array
    {
        return [
            'name' => static::eventName(),
            'params' => [
                'debug_mode' => 1,
                'session_id' => MeasurementProtocolService::getSessionId(),
            ],
        ];
    }

    public static function convertData(array $data): array
    {
        return [];
    }
}