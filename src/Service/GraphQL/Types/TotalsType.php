<?php

namespace App\Service\GraphQL\Types;

use App\Service\GraphQL\Field;

class TotalsType implements TypeInterface
{
    public static function fields(): array
    {
        return [
            new Field('subtotal', MoneyType::fields()),
            new Field('total_tax', TaxType::fields()),
            new Field('taxes', TaxItemType::fields()),
            new Field('discounts', DiscountType::fields()),
            new Field('total_shipping', MoneyType::fields()),
            new Field('grand_total', MoneyType::fields()),
        ];
    }
}