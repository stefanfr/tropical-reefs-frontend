<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\PostScoreContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class PostScore extends AbstractEventModel implements PostScoreContract
{
    protected static string $eventName = 'post_score';

    public function __construct(
        protected readonly int     $score,
        protected readonly ?int    $level,
        protected readonly ?string $character,
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
                    'score' => $this->score,
                    'level' => $this->level,
                    'character' => $this->character,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'score' => $data['score'],
            'level' => $data['level'],
            'character' => $data['character'],
        ];
    }
}