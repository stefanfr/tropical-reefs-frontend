<?php

namespace App\Twig\Components\Checkout\Address;

use App\DataClass\Checkout\Address\Address;
use App\Form\Checkout\Address\AddressType;
use App\Service\Api\Magento\Checkout\MagentoCheckoutAddressApiService;
use App\Service\Postcode\PostcodeService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(name: 'checkout_address', template: 'components/checkout/address/index.html.twig')]
final class AddressComponent extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(writable: true)]
    public string $type = 'shipping';

    #[LiveProp(writable: true)]
    public ?array $lookupDetails = null;

    #[LiveProp(writable: true, fieldName: 'data')]
    public ?Address $address = null;

    public false|array $errors = false;

    public function __construct(
        protected PostcodeService                  $postcodeService,
        protected MagentoCheckoutAddressApiService $magentoCheckoutAddressApiService,
    )
    {
    }

    #[LiveAction]
    public function saveAddress(): null|RedirectResponse
    {
        $this->submitForm();

        $this->address = $this->getFormInstance()->getData();
        $this->address
            ->setStreet($this->lookupDetails['street'])
            ->setCity($this->lookupDetails['place']);

        $this->magentoCheckoutAddressApiService->setCustomerEmail($this->address->getCustomerEmail());

        $this->errors = match ($this->type) {
            'shipping' => $this->magentoCheckoutAddressApiService->saveShippingAddressDetails($this->address),
            'billing' => $this->magentoCheckoutAddressApiService->saveBillingAddressDetails($this->address),
        };

        if ( ! $this->errors) {
            return $this->redirectToRoute('app_checkout_shipment');
        }

        return null;
    }

    #[LiveAction]
    public function postcodeLookup(): void
    {
        $this->submitForm();
        $this->address = $this->getFormInstance()->getData();

        if ( ! $this->address->getPostcode() || ! $this->address->getHouseNr()) {
            return;
        }

        try {
            $this->lookupAddress();
        } catch (InvalidArgumentException $e) {
        }
    }

    protected function instantiateForm(): FormInterface
    {
        $this->address = new Address();
        $form = $this->createForm(AddressType::class, $this->address);

        if ($this->address->getPostcode() && $this->address->getHouseNr()) {
            $this->lookupAddress();
        }

        return $form;
    }

    protected function lookupAddress(): void
    {
        $this->lookupDetails = $this->postcodeService->lookup(
            $this->address->getPostcode(),
            $this->address->getHouseNr(),
            $this->address->getAdd(),
        );
    }
}