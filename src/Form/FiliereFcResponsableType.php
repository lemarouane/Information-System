<?php

namespace App\Form;

use App\Entity\FiliereFcResponsable;
use App\Entity\Personnel;
use App\Entity\FiliereFc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class FiliereFcResponsableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('annee')

            ->add('responsable' , EntityType::class, array(
                'class' => Personnel::class,
                'choice_label' =>  function ($personnel) {
                    return $personnel->getNom().' '.$personnel->getPrenom();
                },
                'mapped' => true,
                'label' => 'responsable',
                'placeholder' => '------------',
                ))

                ->add('filiereFc' , EntityType::class, array(
                    'class' => FiliereFc::class,
                    'choice_label' =>  function ($fil) {
                        return $fil->getNomFiliere().' - '.$fil->getCodeVersion();
                    },
                    'mapped' => true,
                    'label' => 'filiere_fc',
                    'placeholder' => '------------',
               )); 

       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FiliereFcResponsable::class,
        ]);
    }
}
