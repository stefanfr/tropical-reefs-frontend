<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SelectPromotionContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class Share extends AbstractEventModel implements SelectPromotionContract
{
    protected static string $eventName = 'share';

    public function __construct(
        protected readonly ?string $method,
        protected readonly ?string $itemId,
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
                    'method' => $this->method,
                    'item_id' => $this->itemId,
                    'content_type' => $this->contentType,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'method' => $data['method'] ?? null,
            'itemId' => $data['item_id'] ?? null,
            'contentType' => $data['content_type'] ?? null,
        ];
    }
}