<?php

namespace App\Form;

use App\Entity\Grades;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Corps;

class GradesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designationFR',TextType::class, ['label' => 'design_fr'])
            ->add('designationAR',TextType::class, ['label' => 'design_ar'])
            ->add('corpsId' , EntityType::class, array(
                'class' => Corps::class,
                'choice_label' => 'designationFR',
                'mapped' => true,
                'label' => 'corps',
                'placeholder' => '------------',
                'data' => $options['data']->getCorpsId()));

        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grades::class,
        ]);
    }
}
