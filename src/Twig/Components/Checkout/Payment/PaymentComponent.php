<?php

namespace App\Twig\Components\Checkout\Payment;

use App\Service\Api\Magento\Checkout\MagentoCheckoutCartApiService;
use App\Service\Api\Magento\Checkout\MagentoCheckoutPaymentApiService;
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
    public array $totals = [];

    #[LiveProp(writable: true)]
    public array $cartItems = [];

    #[LiveProp(writable: true)]
    public array $paymentMethods = [];

    #[LiveProp(writable: true)]
    public ?string $selectedPaymentMethod = null;

    #[LiveProp(writable: true)]
    public ?bool $useCoupon = false;

    #[LiveProp(writable: true)]
    public ?string $couponCode = null;

    #[LiveProp(writable: true)]
    public ?bool $couponIsValid = null;

    public ?string $couponErrorMessage = null;

    public false|array $errors = false;

    public function __construct(
        protected MagentoCheckoutCartApiService    $magentoCheckoutCartApiService,
        protected MagentoCheckoutPaymentApiService $magentoCheckoutPaymentApiService,
    )
    {
    }

    #[LiveAction]
    public function getTotals(): array
    {
        return $this->totals;
    }

    #[LiveAction]
    public function setSelectedPaymentMethod(#[LiveArg] string $methodCode): void
    {
        $this->selectedPaymentMethod = $methodCode;
    }

    #[LiveAction]
    public function changeUseCoupon(): void
    {
        if ( ! $this->useCoupon) {
            $this->removeCoupon();
        }
    }

    #[LiveAction]
    public function removeCoupon(): void
    {
        $cart = $this->magentoCheckoutPaymentApiService->removeCoupon();

        if ( ! array_key_exists('errors', $cart)) {
            $this->couponIsValid = null;
            $this->couponCode = null;
            $this->useCoupon = false;

            $this->totals = $this->magentoCheckoutCartApiService->formatTotals($cart);
        }
    }

    #[LiveAction]
    public function applyCoupon(): void
    {
        $cart = $this->magentoCheckoutPaymentApiService->applyCoupon($this->couponCode);

        if (array_key_exists('errors', $cart)) {
            $this->couponErrorMessage = $cart[0]['message'];
            $this->couponIsValid = false;

            return;
        }

        $this->totals = $this->magentoCheckoutCartApiService->formatTotals($cart);

        $this->couponErrorMessage = null;
        $this->couponIsValid = true;
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