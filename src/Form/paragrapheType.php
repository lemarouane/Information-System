<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\ArticlePE;

class paragrapheType extends AbstractType
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
            ->add('numParagraphe',IntegerType::class, array('label' => 'N° Paragraphe:',
                'attr' => array(
                  'class' => 'form-control',
                  'placeholder' => 'N° Paragraphe' ,
                  'min' => 0 
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
                


            ))
            ->add('articlePE',EntityType::class, array('label' => 'Article:',
                'class' => ArticlePE::class,
                'choice_label' => function ($articlePE) {
                        return $articlePE->getLibelle();
                    },
                'attr' => array(
                  'class' => 'form-select',
                  'placeholder' => 'Article' ,
                  'min' => 0 
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
                


          ))
            ;            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Paragraphe',

        ));
    }

    public function getName()
    {
        return 'paragraphetype';
    }
}
