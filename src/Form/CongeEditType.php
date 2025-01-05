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
use Symfony\Component\Form\Extension\Core\Type\TextType;
class CongeEditType extends AbstractType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', TextType::class, [ 'disabled' => true  ])
        ->add('dateDebut',DateType::class, array('widget' => 'single_text' ,  
        'required' => true , 
        'label_attr' => ['class' => 'form-label'],
        'html5' => false,
        ))
        ->add('dateReprise',DateType::class, array('widget' => 'single_text' ,
        'required' => true ,
        'label_attr' => ['class' => 'form-label'],
        'html5' => false,
            ))

          ->add('typeConge', ChoiceType::class, [
            'placeholder' => '------Selectionner Type Congé------',
            'disabled' => true ,
                'choices'  => [
                    'Conge Normale' => 'N',
                    'Conge Exeptionnel' => 'E',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => true ,
            ])

            ->add('nbJour',IntegerType::class, array('attr' => array(
                'min' => '0',
                'max' => '22',
                'placeholder' => '--',
                'readonly' => true,
                'required' => true ,
                )))
 
            ->add('Annee_encours', CheckboxType::class, [
                'label'    => 'Annee En Cours '.' ('. Date("Y").')',
                'label_attr' => [
                    'class' => 'form-label',
                 ],
                'required' => false,
                'mapped' => false ,
                'disabled' => true
            ])

           ->add('Annee_precedente', CheckboxType::class, [
                'label'    => 'Annee Precedente' . ' '.'('. intval(Date("Y")-1).')',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => false,
                'mapped' => false ,
                'disabled' => true
            ])

            ->add('motifs' , TextareaType::class, [
                'label_attr' => [
                'class' => 'form-label',
            ]])

            ->add('annee' , IntegerType::class, [
                 'label_attr' => ['hidden' => true],
                 'attr' => ['hidden' => true]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
