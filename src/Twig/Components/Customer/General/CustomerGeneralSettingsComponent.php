<?php

namespace App\Twig\Components\Customer\General;

use App\DataClass\Customer\CustomerAccount;
use App\Form\Customer\General\GeneralSettingsType;
use App\Service\Api\Magento\Customer\Account\General\MagentoCustomerAccountGeneralMutation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent(name: 'customer_general_settings', template: 'components/customer/general/settings.html.twig')]
final class CustomerGeneralSettingsComponent extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        protected MagentoCustomerAccountGeneralMutation $magentoCustomerAccountGeneralMutation,
    )
    {
    }

    #[LiveProp(writable: true, fieldName: 'data')]
    public ?CustomerAccount $customerAccount = null;

    public false|array $errors = false;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(GeneralSettingsType::class, $this->customerAccount);
    }

    #[LiveAction]
    public function saveSettings()
    {
        $this->submitForm();

        $this->customerAccount = $this->getFormInstance()->getData();

        $this->errors = $this->magentoCustomerAccountGeneralMutation->saveSettings($this->customerAccount);

        if ( ! $this->errors) {
            return $this->redirectToRoute('app_checkout_shipment');
        }

        return null;
    }
}