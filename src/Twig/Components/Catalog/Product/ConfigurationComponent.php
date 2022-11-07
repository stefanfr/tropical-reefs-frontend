<?php

namespace App\Twig\Components\Catalog\Product;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('catalog_product_configuration', 'components/catalog/product/configuration.html.twig')]
final class ConfigurationComponent
{
    use DefaultActionTrait;

    public function __construct(
        protected MagentoCheckoutCartApiService $magentoCheckoutCartApiService,
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
    public function addProductToCart()
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
}
