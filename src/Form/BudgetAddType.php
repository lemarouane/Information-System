<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\BudgetEntreeType;

use Doctrine\ORM\EntityRepository;


class BudgetAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 




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
)) ;
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Budget',

        ));
    }

    public function getName()
    {
        return 'budgettype';
    }
}
