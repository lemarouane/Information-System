<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('code',TextType::class, array('label' => 'code',
        'attr' => array(
        'class' => 'form-control',
        ),
        'label_attr' => array(
            'class' => 'form-label',
        )
    ))
    ->add('designation',TextType::class, array('label' => 'designation',
        'attr' => array(
        'class' => 'form-control',
        ),
        'label_attr' => array(
            'class' => 'form-label',
        )
    ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}