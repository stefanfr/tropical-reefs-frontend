<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\RefundContract;
use HappyHorizon\GA\MeasurementProtocol\Models\Item;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class Refund extends AbstractEventModel implements RefundContract
{
    protected static string $eventName = 'refund';

    public function __construct(
        protected readonly string  $currency,
        protected readonly string  $transactionId,
        protected readonly float   $value,
        protected readonly ?string $coupon,
        protected readonly ?float  $shipping,
        protected readonly ?float  $tax,
        protected readonly array   $items = [],
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
                    'currency' => $this->currency,
                    'transaction_id' => $this->transactionId,
                    'value' => $this->value,
                    'coupon' => $this->coupon,
                    'shipping' => $this->shipping,
                    'tax' => $this->tax,
                    'items' => array_map(static fn(Item $item) => $item->toArray(), $this->items),
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'currency' => $data['base_currency_code'],
            'transaction_id' => $data['transaction_id'],
            'value' => $data['base_grand_total'],
            'coupon' => $data['coupon_code'] ?? null,
            'shipping' => $data['shipping_incl_tax'],
            'tax' => $data['base_tax_amount'],
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