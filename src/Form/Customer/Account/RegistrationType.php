<?php

namespace App\Form\Customer\Account;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Firstname',
                    'aria-label' => 'Firstname',
                ],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Lastname',
                    'aria-label' => 'Lastname',
                ],
            ])
            ->add('email', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'E-mail address',
                    'aria-label' => 'E-mail address',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'placeholder' => 'Password',
                        'aria-label' => 'Password',
                        'class' => 'input input-bordered w-full',
                    ],
                ],
                'second_options' => [
                    'label' => 'Repeat password',
                    'attr' => [
                        'placeholder' => 'Repeat password',
                        'aria-label' => 'Repeat password',
                        'class' => 'input input-bordered w-full',
                    ],
                ],
            ])
            ->add('is_subscribed', CheckboxType::class, [
                'required' => false,
                'error_bubbling' => false,
                'constraints' => [new Optional()],
                'attr' => [
                    'class' => 'checkbox checkbox-accent',
                    'placeholder' => 'Subscribe to our newsletter',
                    'aria-label' => 'Subscribe to our newsletter',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
