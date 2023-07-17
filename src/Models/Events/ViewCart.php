<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\ViewCartContract;
use HappyHorizon\GA\MeasurementProtocol\Models\Item;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class ViewCart extends AbstractEventModel implements ViewCartContract
{
    protected static string $eventName = 'view_cart';

    public function __construct(
        protected readonly float  $value,
        protected readonly array  $items,
        protected readonly string $currency = 'EUR',
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
                    'items' => array_map(static fn(Item $item) => $item->toArray(), $this->items),
                    'currency' => $this->currency,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'value' => $data['base_grand_total'],
            'currency' => $data['base_currency_code'],
            'items' => array_map(static function ($item) {
                return new Item(
                    itemId: $item['extension_attributes']['sku'],
                    itemName: $item['name'],
                    affiliation: MeasurementProtocolService::getAffiliation(),
                    discount: $item['base_discount_amount'],
                    price: $item['row_total_incl_tax'],
                    quantity: $item['qty'],
                );
            }, $data['items']),
        ];
    }
}