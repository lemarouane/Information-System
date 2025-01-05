<?php

namespace App\Form;

use App\Entity\Echelon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Grades;
class EchelonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation',TextType::class, array('label' => 'design'))
            ->add('nbAnnee',IntegerType::class, array('label' => 'nb_Annee'))
            ->add('grade' , EntityType::class, array(
                'class' => Grades::class,
                'label' => 'grade',
                'choice_label' => 'designationFR',
                'mapped' => true,
                'placeholder' => '------------',
                'data' => $options['data']->getGrade()));
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Echelon::class,
        ]);
    }
}
