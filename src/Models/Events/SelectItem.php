<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Models\Events\SelectItemContract;
use HappyHorizon\GA\MeasurementProtocol\Models\Item;
use HappyHorizon\GA\MeasurementProtocol\Services\MeasurementProtocolService;

class SelectItem extends AbstractEventModel implements SelectItemContract
{
    protected static string $eventName = 'select_item';

    public function __construct(
        protected readonly ?string $itemListId,
        protected readonly ?string $itemListName,
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
                    'item_list_id' => $this->itemListId,
                    'item_list_name' => $this->itemListName,
                    'items' => array_map(static fn(Item $item) => $item->toArray(), $this->items),
                ]
            )
        ];
    }

    public static function convertData(array $data): array
    {
        return [
            'itemListId' => $data['item_list_id']?? null,
            'itemListName' => $data['item_list_name']?? null,
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