<?php

namespace App\Form;

use App\Entity\EchelonAv;
use App\Entity\Echelon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EchelonAvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rapide',IntegerType::class, array('label' => 'rapide'))
            ->add('exceptionnel',IntegerType::class, array('label' => 'excep'))
            ->add('normale',IntegerType::class, array('label' => 'normale'))

            ->add('etatActuel' , EntityType::class, array(
                'label' => 'etat_act',
                'class' => Echelon::class,
                'choice_label' => 'designation',
                'mapped' => true,
                'placeholder' => '------------',
                'data' => $options['data']->getEtatActuel()))

            ->add('etatPropose' , EntityType::class, array(
                    'label' => 'etat_prop',
                    'class' => Echelon::class,
                    'choice_label' => 'designation',
                    'mapped' => true,
                    'placeholder' => '------------',
                    'data' => $options['data']->getEtatPropose()))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EchelonAv::class,
        ]);
    }
}
