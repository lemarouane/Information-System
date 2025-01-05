<?php

namespace App\Form;

use App\Entity\Filiere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Cycle;

class FiliereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomFiliere',TextType::class, ['label' => 'etablissement'])
            ->add('codeEtab',TextType::class, ['label' => 'codeEtab'])
            ->add('codeApo',TextType::class, ['label' => 'codeApo'])
            ->add('cycle' , EntityType::class, array(
                'class' => Cycle::class,
                'choice_label' => 'nomCycle',
                'label' => 'nom_cycle',
                'mapped' => true,
                'placeholder' => '------------',
                'data' => $options['data']->getCycle()));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filiere::class,
        ]);
    }
}
