<?php

namespace App\Form;

use App\Entity\Personnel;
use App\Entity\RubriqueRecette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\DBAL\Connection;
class PaiementprojetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('montant',NumberType::class, array('label' => 'montant',
                'attr' => array(
                'class' => 'form-control',
                'min' => 0 
                ),
                'required' => true ,
                'label_attr' => array(
                'class' =>'form-label'
                )))


                ->add('numRP',IntegerType::class, array('label' => 'numRP',
                'attr' => array(
                'class' => 'form-control',  
                'min' => 0 ,
      
                'mapped'=> true,
 
                ),
                'label_attr' => array(
                'class' =>'form-label'
                )))

                
                ->add('commentaire',TextType::class, array('label' => 'commentaire',
                'attr' => array(
                'class' => 'form-control',  
                'mapped'=> true,
 
                ),
                'label_attr' => array(
                'class' =>'form-label'
                )))
                ->add('intitule',TextType::class, array('label' => 'intitule',
                'attr' => array(
                'class' => 'form-control',  
                'mapped'=> true,
 
                ),
                'label_attr' => array(
                'class' =>'form-label'
                )))

                ->add('emetteur',TextType::class, array('label' => 'emetteur',
                'attr' => array(
                'class' => 'form-control',  
                'mapped'=> true,
 
                ),
                'label_attr' => array(
                'class' =>'form-label'
                )))


         

            ->add('datePaiement',DateType::class, array('widget' => 'single_text','required'=>true,'mapped'=> true,
                                                      'html5' => false,
                                                      'attr' => ['class' => 'result form-control js-datePaie'],
                                                      'label_attr' => ['class' => 'form-label'],
                                                      'label' => 'date_paiement'
                                                      ))
            ->add('dateOperation',DateType::class, array('widget' => 'single_text','required'=>false,
                                                      'html5' => false,
                                                      'label' => 'date_op',
                                                      'label_attr' => ['class' => 'form-label'],
                                                      'attr' => ['class' => 'result form-control js-dateOpe']))
            ->add('numOperation',TextType::class, array('label' => 'num_op',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
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
            ->add('modePaiement', ChoiceType::class, [
                    'attr' => array(
                        'class' => 'form-select',
                    ),
                   'placeholder' => '------------',
                    'choices'  => [
                        'Virement' => 'Virement', 
                        "Virement depuis l'étranger" => "Virement etrange", 
                        'Versement espèce' => 'Versement espece', 
            
                       
                    ],
                   // 'data' => 'Virement',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
               
                    'required' => true ,
                    'label' => 'mode_paiement' 
                    
                ])
    

            ->add('rubrique',EntityType::class, array('label' => 'rubrique',
                    'class' => RubriqueRecette::class,
                    'placeholder' => '------------',
                    'choice_label' => function ($rubrique) {
                            return $rubrique->getLibelle();
                        },
                    'attr' => array(
                      'class' => 'form-select',
                      'placeholder' => 'Rubrique' ,
                    ),
                    'label_attr' => array(
                      'class' =>'form-label'
                    )));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Paiementprojet'
        ));
    }

    public function getName()
    {
        return 'PaiementprojetType';
    }
}
