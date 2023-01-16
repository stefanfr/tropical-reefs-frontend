<?php

namespace App\Twig\Components\Customer\Address;

use App\DataClass\Customer\Address\CustomerAddress;
use App\Form\Customer\Address\CustomerAddressType;
use App\Service\Api\Magento\Customer\Account\Address\MagentoCustomerAddressMutationService;
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

#[AsLiveComponent(name: 'customer_address', template: 'components/customer/address/index.html.twig')]
final class CustomerAddressComponent extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp(writable: true)]
    public ?array $lookupDetails = null;

    #[LiveProp(writable: true, fieldName: 'data')]
    public ?CustomerAddress $address = null;

    public false|array $errors = false;

    public function __construct(
        protected PostcodeService                       $postcodeService,
        protected MagentoCustomerAddressMutationService $magentoCustomerAddressMutationService
    )
    {
    }

    protected function instantiateForm(): FormInterface
    {
        $form = $this->createForm(CustomerAddressType::class, $this->address);

        if ($this->address->getPostcode() && $this->address->getHouseNr()) {
            $this->lookupAddress();
        }

        return $form;
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

    #[LiveAction]
    public function saveAddress(): null|RedirectResponse
    {
        $this->submitForm();

        $this->address = $this->getFormInstance()->getData();
        $this->address
            ->setStreet($this->lookupDetails['street'])
            ->setCity($this->lookupDetails['place']);

        $this->errors = $this->magentoCustomerAddressMutationService->saveAddress($this->address);

        if ( ! $this->errors) {
            return $this->redirectToRoute('app_customer_address');
        }

        return null;
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