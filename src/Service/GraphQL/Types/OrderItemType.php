<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class OrderItemType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('status'),
            new Field('small_image'),
            new Field('product_sku'),
            new Field('product_name'),
            new Field('product_url_key'),
            new Field('quantity_ordered'),
            new Field('discounts', DiscountType::fields()),
            new Field('product_sale_price', MoneyType::fields()),
            new Field('product_sale_row_total', MoneyType::fields()),
            new Field('selected_options', OrderItemOptionType::fields()),
            new Field('product_sale_price_incl_tax', MoneyType::fields()),
            new Field('product_sale_row_total_incl_tax', MoneyType::fields()),
        ];
    }
}