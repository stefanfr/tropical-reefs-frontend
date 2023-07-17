<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use App\Service\MeasurementProtocolService;

class AddPaymentInfo extends AbstractEventModel
{
    protected static string $eventName = 'add_payment_info';

    public function __construct(
        protected readonly string  $currency,
        protected readonly float   $value,
        protected readonly array   $items,
        protected readonly ?string $coupon = null,
        protected readonly ?string $paymentType = null,
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
                    'coupon' => $this->coupon,
                    'currency' => $this->currency,
                    'payment_type' => $this->paymentType,
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'currency' => $data['base_currency_code'],
            'value' => $data['base_grand_total'],
            'coupon' => $data['coupon_code'] ?? null,
            'paymentType' => $data['method']['title'],
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