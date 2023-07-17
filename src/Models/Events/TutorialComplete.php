<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\TutorialCompleteContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class TutorialComplete extends AbstractEventModel implements TutorialCompleteContract
{
    protected static string $eventName = 'tutorial_complete';

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