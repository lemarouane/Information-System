<?php

namespace App\Form;

use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Utilisateurs;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class DemandeType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('numdemande',TextType::class, array('label' => 'Nº demande:',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Nº demande' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))            
            ->add('demandelignes', CollectionType::class, [ 
                'label' => 'lignes de demande',
                'entry_type' => DemandeLigneType::class,  
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
            'data_class' => 'App\Entity\Demande',

        ));
    }

    public function getName()
    {
        return 'demandetype';
    }
}
