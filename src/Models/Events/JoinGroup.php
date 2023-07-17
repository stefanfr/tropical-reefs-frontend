<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\JoinGroupContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class JoinGroup extends AbstractEventModel implements JoinGroupContract
{
    protected static string $eventName = 'join_group';

    public function __construct(
        protected readonly string $groupId,
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
                    'group_id' => $this->groupId,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'groupId' => $data['params']['group_id'],
        ];
    }
}