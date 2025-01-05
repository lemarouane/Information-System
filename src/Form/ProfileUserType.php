<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class ProfileUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


         $builder
            ->add('email',TextType::class, ['label' => 'E-mail'])

            ->add('nomUtilisateur', TextType::class, ['label' => 'Username'])

            ->add('nom', TextType::class, array(  
                'mapped' => false,
                'label' => 'nom',
               ))
            ->add('prenom', TextType::class, array(  
                'mapped' => false,
                'label' => 'prenom',
               ))




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
            'maxSize' => '2024k',
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


           ->add('locale',ChoiceType::class, array(
            'choices' => array('Français' => 'fr-FR', 'English' => 'en-GB' , 'عربي' => 'ar-AR', 'Español' => 'es-ES'),
            'label_attr' => [
                'class' => 'form-label',
            ],
            'required' => true,
            'placeholder' => '-----',
            'label' => 'Language'
        ))




        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
