<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Paragraphe;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ReceptionType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('numreception',TextType::class, array('label' => 'num_reception',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('datereception',DateType::class, array('label' => 'date_reception','widget' => 'single_text' ,  
            'required' => true , 
            'attr' => ['class' => 'result form-control js-dateReception'],
            'label_attr' => ['class' => 'form-label'],
            'html5' => false,
            ))
            ->add('fournisseur',EntityType::class, array('label' => 'fournisseur',
                'class' => Fournisseur::class,
                'placeholder' => '------------',
                'choice_label' => function ($fournisseur) {
                        return $fournisseur->getRaisonSociale();
                    },
                'attr' => array(
                  'class' => 'form-select',
                  'placeholder' => '' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

            ))
             /*->add('raisonsociale', TextType::class, [
                'data' => '-',
            ]) TextType::class, array('label' => 'raison sociale du fournisseur:',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'raison sociale' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            )) */ 
            ->add('numlivraison',TextType::class, array('label' => 'num_livraison',
                'attr' => array(
                  'class' => 'form-control',
                  'placeholder' => '' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),



            ))
            ->add('bcaoautre',ChoiceType::class, array(
                'choices' => array('Bon de commande' => 'BC', 'Appel d\'offre' => 'AO','Autres sources' => 'A'),
                'multiple' => false,
                'label' => 'type',
                'attr' => array(
                  'class' => 'form-select',  
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
            ))
            ->add('exoneretva', HiddenType::class, [
                'data' => 0,
            ])/* , CheckboxType::class, [
                'label'    => 'exonere tva',
                'required' => false,
            ]) */
            ->add('numbcao',TextType::class, array('label' => 'num_bon_commande',
                'attr' => array(
                  'class' => 'form-control',
                  'placeholder' => '' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),



            ))
            ->add('neuf', ChoiceType::class, [
                'choices'  => [
                    'Neuf' => true,
                    'Seconde main' => false,
                ],
                'attr' => array(
                    'class' => 'form-select',
                    'placeholder' => '' ,
                  ),
                  'label_attr' => array(
                    'class' =>'form-label'
                  ),
            ])
            ->add('totalht', HiddenType::class, [
                'data' => 0,
            ])
            ->add('totalttc', HiddenType::class, [
                'data' => 0,
            ])
            ->add('totaltva', HiddenType::class, [
                'data' => 0,
            ])
            ->add('receptionlignes', CollectionType::class, [ 
                'label' => 'reception lignes',
                'entry_type' => ReceptionLigneType::class,  
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


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Reception',

        ));
    }

    public function getName()
    {
        return 'receptiontype';
    }
}
