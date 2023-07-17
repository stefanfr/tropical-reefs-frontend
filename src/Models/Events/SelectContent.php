<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SelectContentContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class SelectContent extends AbstractEventModel implements SelectContentContract
{
    protected static string $eventName = 'select_content';

    public function __construct(
        protected readonly ?string $contentId,
        protected readonly ?string $contentType,
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
                    'content_id' => $this->contentId,
                    'content_type' => $this->contentType,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'contentId' => $data['content_id'] ?? null,
            'contentType' => $data['content_type'] ?? null,
        ];
    }
}