<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* ->add('code',TextType::class, array('label' => 'Code:',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Code' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            )) */
            ->add('raisonSociale',TextType::class, array('label' => 'raison_sociale',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('email',TextType::class, array('label' => 'email',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('adresse',TextType::class, array('label' => 'adresse',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('ville',TextType::class, array('label' => 'ville',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('telephone',TextType::class, array('label' => 'tel',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('rib',TextType::class, array('label' => 'rib',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('banque',TextType::class, array('label' => 'banque',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('fichier', FileType::class, array('label' => 'ch_fichier','data_class' => null,'required' => false,  'attr' => array('class' => 'form-control')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseur::class,
        ]);
    }
}