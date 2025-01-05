<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\RubriqueRecette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code',TextType::class, array('label' => 'Code CGNC:',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Code CGNC' ,
                    ),
                    'label_attr' => array(
                        'class' => 'form-label',
                    )
                ))
            ->add('nature',TextType::class, array('label' => 'Nature de Recette:',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Nature de Recette' ,
                    ),
                    'label_attr' => array(
                        'class' => 'form-label',
                    )
                ))
            ->add('libelle',TextType::class, array('label' => 'Libelle:',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Libelle' ,
                    ),
                    'label_attr' => array(
                        'class' => 'form-label',
                    )
                ))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
