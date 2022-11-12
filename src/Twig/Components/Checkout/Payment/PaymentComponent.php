<?php

namespace App\Twig\Components\Checkout\Payment;

use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutShipmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'checkout_payment', template: 'components/checkout/payment/index.html.twig')]
final class PaymentComponent extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public array $paymentMethods = [];

    #[LiveProp(writable: true)]
    public ?string $selectedPaymentMethod = null;

    public false|array $errors = false;

    public function __construct(
        protected MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    )
    {
    }

    #[LiveAction]
    public function setSelectedPaymentMethod(#[LiveArg] string $methodCode): void
    {
        $this->selectedPaymentMethod = $methodCode;
    }

    #[LiveAction]
    public function savePaymentMethod(): null|RedirectResponse
    {
        $this->errors = $this->magentoCheckoutPaymentApiService->savePaymentMethod($this->selectedPaymentMethod);

        if ( ! $this->errors) {
            return $this->redirectToRoute('app_checkout_payment');
        }

        return null;
    }
}