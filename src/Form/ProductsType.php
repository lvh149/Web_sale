<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // dd($options);
        $builder
            ->add('name')
            ->add('image')
            ->add('info')
            ->add('price')
            ->add('point')
            ->add('point_give')
            ->add('category', EntityType::class, array(
                'class' => Categories::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                // 'choice_value' => function (?Categories $Categories) {
                //     return $Categories ? $Categories->getId() : '';
                // },
                'mapped' => false,
                'label' => false,
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'data' => 2125,
            ))
        ;
    }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Products::class,
    //     ]);
    // }
}
