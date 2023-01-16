<?php

namespace App\Form\Manager;

use App\Entity\Manager\GeneratedProductModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneratedProductModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('familyVariant')
            ->add('categories')
            ->add('productEnabled')
            ->add('brand')
            ->add('name')
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneratedProductModel::class,
        ]);
    }
}
