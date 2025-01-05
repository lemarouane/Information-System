<?php

namespace App\Form;

use App\Entity\Engagementheure;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Personnel;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class EngagementheureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      $builder
      ->add('jours',ChoiceType::class, array(
             'label' => 'jours',
              'choices' => array('lundi' => 'الاثنين', 'Mardi' => 'الثلاثاء', 'Mercredi' => 'الأربعاء', 'Jeudi' => 'الخميس', 'Vendredi' => 'الجمعة' , 'Samedi' => 'السبت'),
              'multiple' => false,
              'label' => 'Jour',
              'attr' => array(
                'class' => 'form-control',  
              ),
              'label_attr' => array(
                'class' =>'form-label'
              ),
              'row_attr' => [
                  'class' => 'col-12 '
              ],
              
          ))
      ->add('heureDebut',TimeType::class, array(
                'label' => 'h_debut',
                'input'  => 'datetime',
                'widget' => 'choice',
                'attr' => ['class' => 'js-heureDebut'],
                'hours' => array("08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19"),
                'minutes' => array("0"=>"0","15"=>"15","30"=>"30","45"=>"45"),
                'attr' => array(
                'class' => 'form-control',  
                 ),
                 'label_attr' => array(
                    'class' =>'form-label'
                  ),
                  'row_attr' => [
                      'class' => 'col-12 '
                  ],

            ))
      ->add('heureFin',TimeType::class, array(
                'label' => 'h_fin',
                'input'  => 'datetime',
                'widget' => 'choice',
                'attr' => ['class' => 'js-heureFin'],
                'hours' => array("08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19"),
                'minutes' => array("0"=>"0","15"=>"15","30"=>"30","45"=>"45"),
                'attr' => array(
                'class' => 'form-control',  
                 ),
                 'label_attr' => array(
                    'class' =>'form-label'
                  ),
                  'row_attr' => [
                      'class' => 'col-12 '
                  ],
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Engagementheure::class,
        ]);
    }
}
