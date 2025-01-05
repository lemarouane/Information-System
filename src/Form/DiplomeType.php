<?php

namespace App\Form;

use App\Entity\Diplome;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiplomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codeDiplome',TextType::class, array('label' => 'codeDiplome'))
            ->add('designationFR',TextType::class, array('label' => 'design_fr'))
            ->add('designationAR',TextType::class, array('label' => 'design_ar'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Diplome::class,
        ]);
    }
}
