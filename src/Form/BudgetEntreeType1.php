<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\BudgetEntree;
use App\Entity\Budget;
use App\Entity\RubriqueRecette;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class BudgetEntreeType1 extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

          $builder
          

                ->add('budget',EntityType::class, array('label' => 'budget',
                    'class' => Budget::class,
                    'placeholder' => '------------',
                    'choice_label' => function ($budget) {
                        
                            return $budget->getLibelle();
                        },
                        'attr' => array(
                            'class' => 'form-select',
                            'placeholder' => '-----------' ,
                            ),
                            'label_attr' => array(
                            'class' =>'form-label'
                            ),
                    )) 
  
                ->add('rubrique_recette',EntityType::class, array('label' => 'rubrique_recette',
                    'class' => RubriqueRecette::class,
                    'placeholder' => '------------',
                    'choice_label' => function ($rubrique_recette) {
                            return $rubrique_recette->getLibelle();
                        },
                        'attr' => array(
                            'class' => 'form-select',
                            'placeholder' => '-----------' ,
                        ),
                        'label_attr' => array(
                            'class' =>'form-label'
                        ),
                      ))
    
  
  
                ->add('libelle',TextType::class, array('label' => 'libelle',
                        'attr' => array(
                        'class' => 'form-control',
                        ),
                        'label_attr' => array(
                            'class' => 'form-label',
                        )
                    ))

                ->add('annee',IntegerType::class, array('label' => 'annee',
                        'attr' => array(
                        'class' => 'form-control',
                        ),
                        'label_attr' => array(
                            'class' => 'form-label',
                        )
                    ))
 
      
      ->add('montant',NumberType::class, array('label' => 'montant',
      'attr' => array(
      'class' => 'form-control',
      ),
      'label_attr' => array(
          'class' => 'form-label',
      )
  ))
  
  



        
        ;
     
        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => BudgetEntree::class
        ));
    }


    public function getName()
    {
        return 'BudgetEntreeType';
    }

}
