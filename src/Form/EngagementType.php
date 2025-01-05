<?php

namespace App\Form;

use App\Entity\Engagement;
use App\Form\OrdreMission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Personnel;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EngagementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('typeEngagement',ChoiceType::class, array(
           'label' => 'type',
            'choices' => array('Cours' => 'Cours', 'TD' => 'TD', 'TP' => 'TP', 'Controle' => 'Controle', 'Surveillances' => 'Surveillances'),
            'multiple' => false,
            'label' => 'Type d\'engagement:',
            'attr' => array(
              'class' => 'form-control',  
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            'row_attr' => [
                'class' => 'col-6 '
            ],
            'placeholder' => '------------',
        ))
    ->add('matiere',TextType::class, array('label' => 'matiere',
            'attr' => array(
              'class' => 'form-control',
              'placeholder' => 'Matière'  
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            'row_attr' => [
                'class' => 'col-6 '
            ],


      ))
    ->add('dateFait',DateType::class, array('label' => 'date_fait','widget' => 'single_text','label' => 'Date de Rattrapage:','html5' => false,
        'attr' => array(
              'class' => 'form-control js-eng-datepicker',
              'placeholder' => 'Date de Rattrapage' 
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            'row_attr' => [
              'class' => 'col-6 '
          ],

           
        ))
    ->add('personnel', EntityType::class, array(
          'class' => Personnel::class,
          'choice_label' => 'nom',
          'mapped' => true,
          'label' => 'nom_remp' ,
          'attr' => array(
              'class' => 'form-control',  
            ),
          'label_attr' => array(
              'class' =>'form-label'
            ),
          'row_attr' => [
                'class' => 'col-6 '
            ],
          'placeholder' => '------------',
          ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Engagement::class,
        ]);
    }
}
