<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\Parameters;
use App\Repository\ParametersRepository;
use App\Repository\ProductsRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Validator\Constraints\File;

class ProductsType extends AbstractType
{
    public function __construct(ParametersRepository $parametersRepository)
    {
        $this->parametersRepository = $parametersRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $arrParam = $options['data']->getParameters()->toarray();
        // if ($options['data']->getParameters()->toarray()) {
        //     $arrParam = $options['data']->getParameters()->toarray();
        // }
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
            ->add('price', TypeTextType::class, [
                'required' =>false,
            ])
            ->add('point')
            ->add('point_give')
            ->add('category', EntityType::class, array(
                'class' => Categories::class,
                'choice_label' => 'name',
                'mapped' => true,
                'multiple' => false,
                'expanded' => false,
                // 'data' => $options['data']->getCategory(),
            ));
            // ->add('parameters', EntityType::class, array(
            //     'class' => Parameters::class,
            //     'choice_label' => 'value',
            //     'mapped' => true,
            //     'multiple' => true,
            //     'expanded' => true,
            //     'by_reference' => false,
            // ));
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        //     $form = $event->getForm();

        //     /* get groups */
        //     /* check the entity, I'm not sure, it depends on your project */
        //     $parameters = $this->parametersRepository->createQueryBuilder('p')
        //         ->select('p.name')
        //         ->groupBy('p.name')
        //         ->getQuery()
        //         ->getResult();
        //     /* for each group  */
        //     foreach ($parameters as $parameter) {
        //         $parameter=implode($parameter);
        //         $form->add('parameters', EntityType::class, array(
        //             'class' => Parameters::class,
        //             'query_builder' => function (ParametersRepository $er) use ($parameter) {
        //                 return $er->createQueryBuilder('p')
        //                     ->where('p.name = :name')
        //                     ->setParameter('name',  $parameter);
        //             },
        //             'label' => $parameter,
        //             'choice_label' => 'value',
        //             'mapped' => true,
        //             'multiple' => true,
        //             'expanded' => true,
        //             'by_reference' => false,

        //         ));
        //     }
        // });



        // ->add('parameters', EntityType::class, [
        //     'class' => Parameters::class,
        //     'choice_label' => 'value',
        //     'multiple' => true,
        //     'expanded' => true,
        //     // 'choices' =>  $this->parametersRepository->findAll(), // data get all trong db
        //     // 'data' => $options['data']->getParameters()->toarray() // data muon checked
        // ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
