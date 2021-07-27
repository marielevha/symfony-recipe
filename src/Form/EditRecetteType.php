<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EditRecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'short-text',
                    'placeholder' => 'Short Description',
                ]
            ])
            ->add('image', HiddenType::class)
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    "Facile" => 'Facile',
                    "Moyen" => 'Moyen',
                    "Difficile " => 'Difficile',
                    "Expert " => 'Expert',
                ],
                'attr' => [
                    'class' => 'advance-selectable'
                ],
                //'data' => 'Medium',
                //'expanded' => true,
                //'multiple' => false
            ])
            ->add('personne', IntegerType::class)
            ->add('duree')
            //->add('duree', IntegerType::class)
            //->add('date', DateType::class)
            ->add('date', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('categorie', null, [
                'attr' => [
                    'class' => 'advance-selectable'
                ]
            ])
            ->add('auteur', null, [
                'attr' => [
                    'class' => 'dis-none'
                ]
            ])
            ->add('new_image', FileType::class, [
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2000k',
                        /*'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Attention au type'*/
                    ])
                ]
            ])
            //->add('ingredients')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
