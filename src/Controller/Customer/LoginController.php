<?php

namespace App\Controller\Customer;

use App\DataClass\Customer\CustomerAccount;
use App\Form\Customer\Account\LoginType;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractCustomerController
{
    public function __construct(
        protected RequestStack                          $requestStack,
        protected MagentoCustomerAccountQueryService    $magentoCustomerAccountService,
        protected MagentoCustomerAccountMutationService $magentoCustomerAccountMutationService,

    )
    {
        parent::__construct($magentoCustomerAccountService);
    }

    #[Route('/customer/account/login', name: 'app_customer_login')]
    public function index(Request $request): Response
    {
        if ($this->isAuthenticated($request)) {
            return $this->redirectToRoute('app_customer');
        }

        $errors = [];
        $customerAccount = new CustomerAccount;
        $form = $this->createForm(LoginType::class, $customerAccount);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customerAccount = $form->getData();

            $response = $this->magentoCustomerAccountMutationService->generateCustomerToken($customerAccount);

            if (isset($response['errors'])) {
                $errors = $response['errors'];
            }

            if ( ! $errors) {
                try {
                    $session = $this->requestStack->getSession();
                    $this->magentoCustomerAccountMutationService->mergeGuestQuote($session->get('checkout_quote_mask'));
                } catch (SessionNotFoundException $exception) {
                }
                return $this->redirectToRoute('app_customer');
            }
        }

        return $this->render('customer/login/index.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }
}