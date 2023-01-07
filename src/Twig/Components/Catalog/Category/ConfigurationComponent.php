<?php

namespace App\Twig\Components\Catalog\Category;

use App\Service\Api\Magento\Catalog\MagentoCatalogProductApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'catalog_category_configuration', template: 'components/catalog/category/configuration.html.twig')]
final class ConfigurationComponent
{
    use DefaultActionTrait;

    public function __construct(
        protected MagentoCheckoutCartApiService   $magentoCheckoutCartApiService,
        protected MagentoCatalogProductApiService $magentoCatalogProductApiService,
    )
    {
    }

    #[LiveProp(writable: false)]
    public array $product = [];

    #[LiveProp(writable: true)]
    public ?string $selectedSize = null;

    #[LiveProp(writable: true)]
    public int $selectedQty = 1;

    public int $qtySelectorLimit = 10;

    public array $addToCartResponse = [];

    #[LiveAction]
    public function updateProduct()
    {
        $this->product = $this->magentoCatalogProductApiService->collectProduct($this->product['uid'], [$this->selectedSize]);
    }

    #[LiveAction]
    public function addProductToCart(): void
    {
        $addToCartOptions = [
            'sku' => $this->product['sku'],
            'qty' => $this->selectedQty,
        ];

        if (null !== $this->selectedSize) {
            $addToCartOptions['options'][] = $this->selectedSize;
        }

        $this->addToCartResponse = $this->magentoCheckoutCartApiService->addProductToCart($addToCartOptions);
    }

    public function getDiscountAmount(): float
    {
        return $this->product['configurable_product_options_selection']['variant']['price_range']['minimum_price']['discount']['amount_off'] ??
            $this->product['price_range']['minimum_price']['discount']['amount_off'];
    }

    public function getProductRegularPrice(): float
    {
        return $this->product['configurable_product_options_selection']['variant']['price_range']['minimum_price']['regular_price']['value'] ??
            $this->product['price_range']['minimum_price']['regular_price']['value'];
    }

    public function getProductFinalPrice(): float
    {
        return $this->product['configurable_product_options_selection']['variant']['price_range']['minimum_price']['final_price']['value'] ??
            $this->product['price_range']['minimum_price']['final_price']['value'];
    }
}
