<?php

namespace App\Twig\Components\Checkout\Shipping;

use App\Service\Api\Magento\Checkout\MagentoCheckoutShipmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'checkout_shipping', template: 'components/checkout/shipping/index.html.twig')]
final class ShippingComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public array $shippingMethods = [];

    #[LiveProp(writable: true)]
    public ?array $selectedShippingMethod = null;

    #[LiveProp(writable: true)]
    public bool $shippingSameAsBilling = true;

    public false|array $errors = false;

    public function __construct(
        protected MagentoCheckoutShipmentService $magentoCheckoutShipmentService,
    )
    {
    }

    #[LiveAction]
    public function setSelectedShippingMethod(#[LiveArg] string $carrierCode, #[LiveArg] string $methodeCode): void
    {
        $this->selectedShippingMethod = [
            'carrier_code' => $carrierCode,
            'method_code' => $methodeCode,
        ];
    }

    #[LiveAction]
    public function saveShippingMethod(): null|RedirectResponse
    {
        $this->errors = $this->magentoCheckoutShipmentService->saveShippingMethod($this->selectedShippingMethod);

        if ( ! $this->errors) {
            return $this->redirectToRoute('app_checkout_payment');
        }

        return null;
    }
}