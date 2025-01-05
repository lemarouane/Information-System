<?php

namespace App\Form\Etudiant;

use App\Entity\Etudiant\Entreprises;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EntreprisesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule',TextType::class, [ 'label_attr' => ['class' => 'form-label']])
            ->add('secteur',TextType::class, [ 'label' => 'Secteur d\'ctivité','label_attr' => ['class' => 'form-label']])
            ->add('per_contact',TextType::class, [ 'label' => 'Personne à contacter','label_attr' => ['class' => 'form-label']])
            ->add('telephone',TextType::class, [ 'label_attr' => ['class' => 'form-label']])
            ->add('adresse',TextareaType::class,array(
                'label' => 'Adresse',
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Adresse',  
                ),
                'label_attr' => array(
                        'class' =>'form-label'
                ),
                
                ))
            ->add('ville',TextType::class, [ 'label_attr' => ['class' => 'form-label']])
            ->add('convention', ChoiceType::class, [
                'choices'  => [
                    'OUI' => '1',
                    'NON' => '0',
                ],
            ])
            
            ->add('fichier', FileType::class, [
                'label' => 'Invitation',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File()
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entreprises::class,
        ]);
    }
}
