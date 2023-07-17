<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\UnlockAchievementContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class UnlockAchievement extends AbstractEventModel implements UnlockAchievementContract
{
    protected static string $eventName = 'unlock_achievement';

    public function __construct(
        protected readonly string $achievementId,
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
                    'achievement_id' => $this->achievementId,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'achievementId' => $data['achievement_id'] ?? null,
        ];
    }
}