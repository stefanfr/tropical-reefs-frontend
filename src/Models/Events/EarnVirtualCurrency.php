<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\EarnVirtualCurrencyContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class EarnVirtualCurrency extends AbstractEventModel implements EarnVirtualCurrencyContract
{
    protected static string $eventName = 'earn_virtual_currency';

    public function __construct(
        protected readonly float $value,
        protected ?string        $virtualCurrencyName = null,
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
                    'virtual_currency_name' => $this->virtualCurrencyName,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'value' => $data['value'],
            'virtual_currency_name' => $data['virtual_currency_name'] ?? null,
        ];
    }
}