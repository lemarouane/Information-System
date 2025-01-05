<?php

namespace App\Form;

use App\Entity\Ficheheure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Personnel;
use App\Entity\Engagementheure;
use App\Form\EngagementheureType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FicheheureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    { 
        $builder
         
            ->add('etablissement' ,TextType::class, ['label' => 'etablissement', 'label_attr' => ['class' => 'form-label']])
            ->add('ville' ,TextType::class, [ 'label' => 'ville','label_attr' => ['class' => 'form-label']])
            ->add('nbHeure' ,TextType::class, [ 'label' => 'nb_Heures','label_attr' => ['class' => 'form-label']])
            ->add('moisDebut',DateType::class, array('widget' => 'single_text' ,  
            'required' => true , 
            'label_attr' => ['class' => 'form-label'],
            'html5' => false,
            'label' => 'm_debut'
            ))
            ->add('moisFin',DateType::class, array('widget' => 'single_text' ,  
            'required' => true , 
            'label_attr' => ['class' => 'form-label'],
            'html5' => false,
            'label' => 'm_fin'
            ))
      


            ->add('engagements', CollectionType::class, [ 
                'label' => 'engagements',
                'entry_type' => EngagementheureType::class,  
                'entry_options' => [
                  'label' => false,
                  'required' => false
              ],
              'by_reference' => false,
              'allow_add' =>true,
              'allow_delete' =>true,
              ])
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ficheheure::class,
        ]);
    }
}
