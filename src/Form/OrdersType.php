<?php

namespace App\Form;

use App\Entity\Customers;
use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('status')
            // ->add('customer', EntityType::class, array(
            //     'class' => Customers::class,
            //     // 'choice_label' => 'name',
            //     'mapped' => false,
            //     'multiple' => false,
            //     'expanded' => false,
            //     // 'data' => $options['data']->getCategoryId(),
            // ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Orders::class,
        ]);
    }
}
