<?php

namespace App\Form\Customer\General;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Required;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'always_empty' => false,
                'label' => 'Current password',
                'attr' => [
                    'placeholder' => 'Current Password',
                    'aria-label' => 'Current Password',
                    'class' => 'input input-bordered w-full',
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'first_options' => [
                    'always_empty' => false,
                    'label' => 'New password',
                    'attr' => [
                        'placeholder' => 'New password',
                        'aria-label' => 'New password',
                        'class' => 'input input-bordered w-full',
                    ],
                ],
                'second_options' => [
                    'always_empty' => false,
                    'label' => 'Repeat new password',
                    'attr' => [
                        'placeholder' => 'Repeat new password',
                        'aria-label' => 'Repeat new password',
                        'class' => 'input input-bordered w-full',
                    ],
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
