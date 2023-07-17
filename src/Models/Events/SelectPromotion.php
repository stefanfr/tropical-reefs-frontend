<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SelectPromotionContract;
use HappyHorizon\GA\MeasurementProtocol\Models\Item;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class SelectPromotion extends AbstractEventModel implements SelectPromotionContract
{
    protected static string $eventName = 'select_promotion';

    public function __construct(
        protected readonly ?string $creativeName,
        protected readonly ?string $creativeSlot,
        protected readonly ?string $promotionId,
        protected readonly ?string $promotionName,
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
                    'creative_name' => $this->creativeName,
                    'creative_slot' => $this->creativeSlot,
                    'promotion_id' => $this->promotionId,
                    'promotion_name' => $this->promotionName,
                    'items' => array_map(static fn(Item $item) => $item->toArray(), $this->items),
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {

        return [
            'creativeName' => $data['creative_name'] ?? null,
            'creativeSlot' => $data['creative_slot'] ?? null,
            'promotionId' => $data['promotion_id'] ?? null,
            'promotionName' => $data['promotion_name'] ?? null,
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