<?php

namespace App\Controller\Customer;

use App\DataClass\Customer\CustomerAccount;
use App\Form\Customer\Account\RegistrationType;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountRegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    public function __construct(
        protected MagentoCustomerAccountMutationService $magentoCustomerAccountMutationService
    )
    {
    }

    #[Route('/customer/account/register', name: 'app_customer_register', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $errors = [];
        $customerAccount = new CustomerAccount;
        $form = $this->createForm(RegistrationType::class, $customerAccount);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customerAccount = $form->getData();

            $errors = $this->magentoCustomerAccountMutationService->createCustomer($customerAccount);


            if ( ! $errors) {
                return $this->redirectToRoute('app_customer_login');
            }
        }

        return $this->render('customer/register/index.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }
}