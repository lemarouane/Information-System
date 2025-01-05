<?php

namespace App\Form;

use App\Entity\Conge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class CongeType extends AbstractType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateDebut',DateType::class, array('widget' => 'single_text' ,  
        'required' => true , 
        'label_attr' => ['class' => 'form-label'],
        'html5' => false,
        'label' => 'date_debut'
        ))

        ->add('dateReprise',DateType::class, array('widget' => 'single_text' ,
        'required' => true ,
        'label_attr' => ['class' => 'form-label'],
        'html5' => false,
        'label' => 'date_fin'
            ))

          ->add('typeConge', ChoiceType::class, [
            'placeholder' => '------------',
                'choices'  => [
                    'c_normale' => 'N',
                    'c_exp' => 'E',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => true ,
                'label' => 'type'
            ])

            ->add('annee', ChoiceType::class, [
                'placeholder' => '------------',
                'translation_domain' => 'messages',
                    'choices'  => [
                         date("Y") => date("Y"),
                         date("Y") - 1 => date("Y") - 1,
                    ],
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'required' => true ,'label' => 'annee'
                ])

            ->add('nbJour',IntegerType::class, array( 'label' => 'nb_Jours' , 'attr' => array(
           
                'placeholder' => '--',
                'required' => true ,
          
                )))
 
          

            ->add('motifs' , TextareaType::class, [
                'label' => 'motif', 
                'label_attr' => [
                'class' => 'form-label',
           
            ]])

         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
