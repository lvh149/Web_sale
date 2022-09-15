<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\Parameters;
use App\Entity\ProductsParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => array('style' => 'height: 30px'),
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('info')
            ->add('price')
            ->add('point')
            ->add('point_give')
            ->add('category', EntityType::class, array(
                'class' => Categories::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'mapped' => true,
                'multiple' => false,
                'expanded' => false,
                'data' => $options['data']->getCategory(),
            ));
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
