<?php

namespace App\Form\Checkout\Address;

use App\Service\Api\Magento\Checkout\MagentoCheckoutApiService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Required;

class AddressType extends AbstractType
{
    public function __construct(
        protected MagentoCheckoutApiService $magentoCheckoutApiService,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerEmail', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Email address',
                ],
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Firstname',
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Lastname',
                ],
            ])
            ->add('company', TextType::class, [
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Company',
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Phone number',
                ],
            ])
            ->add('country_code', ChoiceType::class, [
                'constraints' => [new Required()],
                'choices' => $this->getCountryValues(),
                'attr' => [
                    'class' => 'select select-bordered w-full',
                    'placeholder' => 'Phone number',
                ],
            ])
            ->add('postcode', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required(), new Regex('/^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i')],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Postcode',
                ],
            ])
            ->add('houseNr', NumberType::class, [
                'required' => true,
                'constraints' => [new Required(), new GreaterThan(0)],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'House nr.',
                ],
            ])
            ->add('add', TextType::class, [
                'required' => false,
                'constraints' => [new Optional()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Add.',
                ],
            ])
            ->add('street', TextType::class, [
                'required' => false,
                'constraints' => [new Optional()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Street',
                ],
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'constraints' => [new Optional()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'City',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    protected function getCountryValues(): array
    {
        $formattedCountries = [];
        $countries = $this->magentoCheckoutApiService->collectCountries();
        foreach ($countries as $country) {
            $formattedCountries[$country['full_name_locale']] = $country['two_letter_abbreviation'];
        }
        return $formattedCountries;
    }
}
