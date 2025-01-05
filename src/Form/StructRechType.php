<?php

namespace App\Form;

use App\Entity\StructRech;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\TypeStructRech;
class StructRechType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelleStructure',TextType::class , ['label'=>'libelle'])
            ->add('abrevStructure',TextType::class , ['label'=>'abrevStr'])
            ->add('typeStructure' , EntityType::class, array(
                'class' => TypeStructRech::class,
                'choice_label' => 'nomStructure',
                'label' => 'type',
                'mapped' => true,
                'placeholder' => '------------',
                'data' => $options['data']->getTypeStructure()));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StructRech::class,
        ]);
    }
}
