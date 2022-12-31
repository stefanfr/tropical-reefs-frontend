<?php

namespace App\Form\Customer\Account;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Required;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('password', PasswordType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'label' => 'Password',
                    'placeholder' => 'Password',
                    'aria-label' => 'Password',
                    'class' => 'input input-bordered w-full',
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
