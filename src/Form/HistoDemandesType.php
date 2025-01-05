<?php

namespace App\Form;

use App\Entity\HistoDemandes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoDemandesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_demande')
            ->add('id_demande')
            ->add('dateEnvoie')
            ->add('dateValidation')
            ->add('statut')
            ->add('niveau')
            ->add('demandeur')
            ->add('validateur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HistoDemandes::class,
        ]);
    }
}
