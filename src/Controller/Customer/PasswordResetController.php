<?php

namespace App\Controller\Customer;

use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountMutationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Required;

class PasswordResetController extends AbstractController
{
    public function __construct(
        protected MagentoCustomerAccountMutationService $magentoCustomerAccountMutationService
    )
    {
    }

    #[Route('/customer/account/forget', name: 'app_customer_password_forget', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $errors = [];
        $form = $this->createFormBuilder()
            ->add(
                'email',
                TextType::class,
                [
                    'required' => true,
                    'error_bubbling' => false,
                    'constraints' => [new Required()],
                    'attr' => [
                        'class' => 'input input-bordered w-full',
                        'placeholder' => 'E-mail address',
                        'aria-label' => 'E-mail address',
                    ],
                ]
            )
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customerData = $form->getData();

            $errors = $this->magentoCustomerAccountMutationService->requestPasswordResetEmail($customerData['email']);

            if (!$errors) {
                return $this->redirectToRoute('app_customer_login');
            }
        }

        return $this->render('customer/password/forget.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    #[Route('/customer/account/createPassword', name: 'app_customer_password_create_password', methods: ['GET', 'POST'])]
    public function createPassword(Request $request): Response
    {
        $errors = [];
        $form = $this->createFormBuilder()
            ->add(
                'email',
                TextType::class,
                [
                    'required' => true,
                    'error_bubbling' => false,
                    'constraints' => [new Required()],
                    'attr' => [
                        'class' => 'input input-bordered w-full',
                        'placeholder' => 'E-mail address',
                        'aria-label' => 'E-mail address',
                    ],
                ]
            )
            ->add(
                'resetPasswordToken',
                HiddenType::class,
                [
                    'required' => true,
                    'error_bubbling' => false,
                    'constraints' => [new Required()],
                    'attr' => [
                        'value' => $request->query->get('token'),
                    ],
                ]
            )
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'first_options' => [
                    'label' => 'New Password',
                    'attr' => [
                        'placeholder' => 'New Password',
                        'aria-label' => 'New Password',
                        'class' => 'input input-bordered w-full',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => [
                        'placeholder' => 'Confirm password',
                        'aria-label' => 'Confirm password',
                        'class' => 'input input-bordered w-full',
                    ],
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customerData = $form->getData();

            $errors = $this->magentoCustomerAccountMutationService->resetPassword($customerData);

            if (!$errors) {
                return $this->redirectToRoute('app_customer_login');
            }
        }

        return $this->render('customer/password/create.html.twig', [
            'token' => $request->query->get('token'),
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }
}