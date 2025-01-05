<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UtilisateursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email' ,TextType::class,[ 'label' => 'Email'])
            ->add('nom', TextType::class, array(  
                'mapped' => false,
                'label' => 'nom'
               ))
            ->add('prenom', TextType::class, array(  
                'mapped' => false,
                'label' => 'prenom'
               ))

            ->add('roles',ChoiceType::class, array(
                  'label' => 'Roles',
                    'choices' => array('Super-Admin' => 'ROLE_SUPER_ADMIN', 'Admin' => 'ROLE_ADMIN', 'Professeur' => 'ROLE_PROF',
                    'Fonctionnaire' => 'ROLE_FONC', 'Chef de Service' => 'ROLE_CHEF_SERV', 'Séc.Générale' => 'ROLE_SG', 'R.H.' => 'ROLE_RH', 'Gest.Absences' => 'ROLE_ABS',
                    'Chef de Structure de Recherche' => 'ROLE_CHEF_STRUCT', 'Chef de Département' => 'ROLE_CHEF_DEP',
                    'Chef de Filiére' => 'ROLE_CHEF_FIL', 'Dir.Adjoint' => 'ROLE_DIR_ADJ', 'Directeur' => 'ROLE_DIR', 'Relation Exterieure' => 'ROLE_SERVICEEXT', 'Scolarite' => 'ROLE_SCOLARITE',
                    'Scolarite FC' => 'ROLE_SCOLARITEFC'),
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => true,
                'expanded' => false,
                'attr' => ['class' => '' , 'style'=>'height:150px;' ],
                'multiple' => true,
                'placeholder' => '------------',
                'label' => 'Roles'
            ))
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'label' => 'security.password',
                
            ])
            ->add('locale',ChoiceType::class, array(
                'choices' => array('Français' => 'fr-FR', 'English' => 'en-GB' , 'عربي' => 'ar-AR', 'Español' => 'es-ES'),
                
                'required' => true,
                'placeholder' => '------------',
                'label' => 'Language'
            ))
     
            ->add('nom_utilisateur',TextType::class, ['label' => 'Username'])
           
            
            ->add('imageName' ,TextType::class, ['mapped' => false])
            ->add('imageSize')
            ->add('imageFile', FileType::class, [
                'label' => 'Profile Picture',
            
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
            
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '5024k',
                        'mimeTypes' => [
                            'application/jpg',
                            'application/x-jpg',
                            'application/png',
                            'application/x-png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG document',
                    ])
                ],
            ])
           // ->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
