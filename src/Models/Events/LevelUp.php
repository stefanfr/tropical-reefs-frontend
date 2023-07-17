<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\LevelUpContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class LevelUp extends AbstractEventModel implements LevelUpContract
{
    protected static string $eventName = 'level_up';

    public function __construct(
        protected readonly ?string $character,
        protected readonly ?int    $level,
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
                    'level' => $this->level,
                    'character' => $this->character,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'level' => $data['level'] ?? null,
            'character' => $data['character'] ?? null,
        ];
    }
}