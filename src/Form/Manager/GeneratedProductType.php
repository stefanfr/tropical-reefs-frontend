<?php

namespace App\Form\Manager;

use App\Entity\Manager\GeneratedProduct;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Required;

class GeneratedProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sku', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Sku',
                ],
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Name',
                ],
            ])
            ->add('description', CKEditorType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Description',
                ],
            ])
            ->add('parent', HiddenType::class)
            ->add('enabled', HiddenType::class)
            ->add('family', HiddenType::class)
            ->add('categories', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Categories',
                ],
            ])
            ->add('brand', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Brand',
                ],
            ])
            ->add('productEnabled', HiddenType::class)
            ->add('supplierCode', HiddenType::class)
            ->add('weight', TextType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Weight',
                ],
            ])
            ->add('salesPrice', NumberType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Sales price',
                ],
            ])
            ->add('purchasePrice', NumberType::class, [
                'required' => true,
                'error_bubbling' => false,
                'constraints' => [new Required()],
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Purchase price',
                ],
            ])
            ->add('eanCode', TextType::class, [
                'required' => false,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Ean code',
                ],
            ])
            ->add('tax', ChoiceType::class, [
                'constraints' => [new Required()],
                'choices' => [
                    'LOW' => '9%',
                    'HIGH' => '21%',
                ],
                'attr' => [
                    'class' => 'select select-bordered w-full',
                    'placeholder' => 'Tax',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneratedProduct::class,
        ]);
    }
}
