<?php

namespace App\Form;

use App\Entity\Personnel;
use App\Entity\RegistreInventaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class DechargeType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            
        ->add('numdecharge',TextType::class, array('label' => 'num_decharge',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('exercice',TextType::class, array('label' => 'exercice',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('local',TextType::class, array('label' => 'locale',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('datedecharge',DateType::class, array('label' => 'date_decharge','widget' => 'single_text' ,  
            'required' => true , 
            'attr' => ['class' => 'result form-control dateReception'],
            'label_attr' => ['class' => 'form-label'],
            'html5' => false,
                ))
                ->add('personnel',EntityType::class, array('label' => 'personnel',
                  'class' => Personnel::class,
                  'placeholder' => '------------',
                  'choice_label' => function ($personnel) {
                          return $personnel->getNom().' '.$personnel->getprenom();
                      },
                  'attr' => array(
                    'class' => 'form-select',
                    'placeholder' => '' ,
                  ),
                  'label_attr' => array(
                    'class' =>'form-label'
                  ),
  
        ))
        ->add('affectations', CollectionType::class, [ 
            'label' => 'affectation',
            'entry_type' => AffectationType::class,  
            'entry_options' => [
              'label' => false,
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
            'data_class' => 'App\Entity\Decharge',

        ));
    }

    public function getName()
    {
        return 'DechargeType';
    }
}
