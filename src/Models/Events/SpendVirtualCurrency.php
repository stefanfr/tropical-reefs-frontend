<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SpendVirtualCurrencyContract;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class SpendVirtualCurrency extends AbstractEventModel implements SpendVirtualCurrencyContract
{
    protected static string $eventName = 'earn_virtual_currency';

    public function __construct(
        protected readonly float   $value,
        protected readonly string  $virtualCurrencyName,
        protected readonly ?string $itemName = null,
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
                    'item_name' => $this->itemName,
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