<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class articlePEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('libelle',TextType::class, array('label' => 'Libelle:',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Libelle' ,
                    ),
                    'label_attr' => array(
                        'class' => 'form-label',
                    )
                ))
            ->add('numArticle',IntegerType::class, array('label' => 'N° Article:',
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'N° Article' ,
                    'min' => 0 
                    ),
                    'label_attr' => array(
                        'class' => 'form-label',
                    )
            ))
            ;            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ArticlePE',

        ));
    }

    public function getName()
    {
        return 'articlePEtype';
    }
}
