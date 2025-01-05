<?php

namespace App\Form\Etudiant;

use App\Entity\Etudiant\Etudiants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EtudiantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email' ,TextType::class,[ 'label' => 'Email'])
            ->add('code' ,TextType::class,[ 'label' => 'num_apogee'])
            ->add('nomUtilisateur' ,TextType::class,[ 'label' => "Nom d'Utilisateur"])
            ->add('nom', TextType::class, array(  
                'mapped' => false,
                'label' => 'nom'
               ))
            ->add('prenom', TextType::class, array(  
                'mapped' => false,
                'label' => 'prenom'
               ))

        
            /* ->add('password', PasswordType::class, [
                'mapped' => false,
                'label' => 'security.password',
                
            ]) */
            ->add('locale',ChoiceType::class, array(
                'choices' => array('Français' => 'fr-FR', 'English' => 'en-GB' , 'عربي' => 'ar-AR', 'Español' => 'es-ES'),
                
                'required' => true,
                'placeholder' => '------------',
                'label' => 'Language'
            ))

            ->add('type',ChoiceType::class, array(
                'choices' => array('FI' => 'FI', 'FC' => 'FC'),
                 
                'required' => true,
                'placeholder' => '------------',
                'label' => 'Type'
            ))
                
                 
    
       
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiants::class,
        ]);
    }
}
