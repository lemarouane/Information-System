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
use App\Form\BudgetEntreeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Doctrine\ORM\EntityRepository;


class BudgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 

        ->add('libelle',TextType::class, array('label' => 'libelle','disabled' => true,
        'attr' => array(
        'class' => 'form-control',
        ),
        'label_attr' => array(
            'class' => 'form-label',
        )
    ))

    ->add('annee',IntegerType::class, array('label' => 'annee','disabled' => true,
    'attr' => array(
    'class' => 'form-control',
    ),
    'label_attr' => array(
        'class' => 'form-label',
    )
))


 

/* 
            ->add('budgetEntrees', CollectionType::class, [ 
                  'entry_type' => BudgetEntreeType::class,  
                  'entry_options' => [
                    'label' =>'', //  $options['label']
                    'help' => '', // $options['help']
                    'required' => false
                ],
                'by_reference' => false,
                'allow_add' =>true, 
                'allow_delete' =>true,
                ])
 */
                ->add('budgetSorties', CollectionType::class, [ 
                    'entry_type' => BudgetSortieType::class,  
                    'entry_options' => [
                      'label' =>'', //  $options['label']
                      'help' => '', // $options['help']
                      'required' => false
                  ],
                  'by_reference' => false,
                  'allow_add' =>true, 
                  'allow_delete' =>true,
                  ])->add('save', SubmitType::class, [
                    'attr' => ['class' => 'btn btn btn-success px-4 col-12 '],
                ]);
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Budget',

        ));
    }

    public function getName()
    {
        return 'Budgettype';
    }
}
