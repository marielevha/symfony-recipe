<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CreeRecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextareaType::class)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'cols' => '30',
                    'rows' => '10'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Attachez une fiche',
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '2000k',
                        'mimeTypes' => [
                            'application/jpeg',
                            'application/jpg',
                            'application/png',
                        ],
                        'mimeTypesMessage' => 'Attention au type'
                    ])
                ]
            ])
            ->add('difficulte')
            ->add('personne')
            ->add('duree')
            ->add('categorie')
            ->add('auteur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
