<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\ProgrammeElementBudgetType;
use App\Entity\Personnel;
use App\Entity\ArticlePE;
use App\Entity\FiliereFcResponsable;
use App\Entity\Paragraphe;
use App\Entity\ProgrammeEmploiBudget;
use App\Entity\ProgrammeElementBudget;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormInterface;

class ProgrammeEmploiBudgetType extends AbstractType
{

  


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

  

           $builder 
             ->add('personne' , EntityType::class, array(
                    'class' => Personnel::class,
                    'placeholder' => '------------',
                    'disabled' => true,
            
                    'choice_label' => function ($personne) {
                        return $personne->getNom().' '.$personne->getPrenom();
                    }))


            ->add('intitule' ,TextType::class,array( 'disabled' => true))
           // ->add('reference',TextType::class,array())


           /*  ->add('montant',NumberType::class, array('label' => 'Montant:',
                    'scale' => 1,
          
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Montant' ,
                    'min' => 0 ,
                    'step' => '.1',
                    
                    ),
                    'label_attr' => array(
                    'class' =>'control-label'
                    ),
                    'row_attr' => [
                        'class' => 'col-sm-6'
                    ],
             )) */
             ->add('articlePE' , EntityType::class, array(
                'class' => ArticlePE::class,
                'mapped' => true,
                'disabled' => true,
                'placeholder' => '------------',
                'choice_label' => function ($articlePE) {
                    return $articlePE->getNumArticle()." - ". $articlePE->getLibelle();
                }))

            ->add('element', CollectionType::class, [ 
                  'entry_type' => ProgrammeElementBudgetType::class,  
                  'entry_options' => [
                    'label' => $options['label'],
                    'help' => $options['help'],
                    'required' => false
                ],
                'by_reference' => false,
                'allow_add' =>true,
                'allow_delete' =>true,
            ]);




                
                
    }
   


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ProgrammeEmploiBudget',

        ));
    }

    public function getName()
    {
        return 'programmeEmploiBudgetType';
    }
}
