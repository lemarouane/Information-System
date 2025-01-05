<?php

namespace App\Form;

use App\Entity\NoteFonctionnaire;
use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class NoteFonctionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             
        ->add('note1',IntegerType::class, array(  'translation_domain' => 'messages',  'label' => 'note1',  'attr' => array(
          
            'min' => '0',
            'max' => '5',
            'placeholder' => '0 - 5',
            )))
         ->add('note2',IntegerType::class, array('label' => 'note2', 'attr' => array(
                'min' => '0',
                'max' => '5',
                'placeholder' => '0 - 5',
                )))
        ->add('note3',IntegerType::class, array('label' => 'note3', 'attr' => array(
                'min' => '0',
                'max' => '3',
                'placeholder' => '0 - 3',
                    )))

         ->add('note4',IntegerType::class, array('label' => 'note4', 'attr' => array(
                        'min' => '0',
                        'max' => '4',
                        'placeholder' => '0 - 4',
                        )))

         ->add('note5',IntegerType::class, array('label' => 'note5' , 'attr' => array(
                    'min' => '0',
                    'max' => '3',
                    'placeholder' => '0 - 3',
                            )))
        ->add('noteAnuelle',IntegerType::class, array('label' => 'note_anuelle','attr' => array(
                    'min' => '0',
                    'max' => '20',
                    'placeholder' => '0',
                    'readonly'=> true,
                                )))

      ->add('annee',IntegerType::class, array('label' => 'annee','attr' => array(
                    'min' => '1920',
                    'max' => '9999',
                    'placeholder' => '0',
                                )))

            ->add('remarque',TextType::class,array('label' => 'remarque'))
            ->add('lien',TextType::class,array('label' => 'remarque'))
           // ->add('personnel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoteFonctionnaire::class,
        ]);
    }
}
