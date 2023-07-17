<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\AddToWishlistContract;
use HappyHorizon\GA\MeasurementProtocol\Models\Item;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class AddToWishlist extends AbstractEventModel implements AddToWishlistContract
{
    protected static string $eventName = 'add_to_wishlist';

    public function __construct(
        protected readonly float  $value,
        protected readonly array  $items,
        protected readonly string $currency = 'EUR',
    )
    {
    }

    public static function convertData(array $data): array
    {
        $product = $data['product'];

        return [
            'value' => ($product['final_price_incl_tax'] * $data['qty']),
            'currency' => $data['currency'] ?? 'EUR',
            'items' => [
                new Item(
                    itemId: $product['sku'],
                    itemName: $product['name'],
                    affiliation: MeasurementProtocolService::getAffiliation(),
                    discount: $product['discount_amount'],
                    itemBrand: $product['brand'],
                    price: $product['final_price_incl_tax'],
                    quantity: $data['qty'],
                )
            ],
        ];
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
}