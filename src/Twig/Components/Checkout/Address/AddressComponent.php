<?php

namespace App\Twig\Components\Checkout\Address;

use App\DataClass\Checkout\Address\Address;
use App\Form\Checkout\Address\AddressType;
use App\Service\Api\Magento\Checkout\MagentoCheckoutAddressApiService;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressQueryService;
use App\Service\Postcode\PostcodeService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
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
    public bool $isLoggedIn = false;

    #[LiveProp(writable: true)]
    public string $type = 'shipping';

    #[LiveProp(writable: true)]
    public ?array $lookupDetails = null;

    #[LiveProp(writable: true)]
    public null|int|string $addressId = null;

    #[LiveProp(writable: true, fieldName: 'data')]
    public ?Address $address = null;

    public false|array $errors = false;

    public function __construct(
        protected RequestStack                       $requestStack,
        protected PostcodeService                    $postcodeService,
        protected MagentoCheckoutAddressApiService   $magentoCheckoutAddressApiService,
        protected MagentoCustomerAddressQueryService $magentoCustomerAddressQueryService
    )
    {
    }

    public function collectAddresses(): array
    {
        static $addresses;
        if (null !== $addresses) {
            return $addresses;
        }
        try {
            $session = $this->requestStack->getSession();
            if ($session->has('customerToken')) {
                $addresses = $this->magentoCustomerAddressQueryService->collectCustomerAddresses();
                return $addresses;
            }
        } catch (SessionNotFoundException $exception) {

        }
        $addresses = [];

        return $addresses;
    }

    #[LiveAction]
    public function saveAddress(): null|RedirectResponse
    {
        if (is_numeric($this->addressId)) {
            $this->errors = match ($this->type) {
                'shipping' => $this->magentoCheckoutAddressApiService->saveShippingAddressId($this->addressId),
                'billing' => $this->magentoCheckoutAddressApiService->saveBillingAddressId($this->addressId),
            };

            if ( ! $this->errors) {
                return $this->redirectToRoute('app_checkout_shipment');
            }

            return null;
        }
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
    public function setAddressId()
    {

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