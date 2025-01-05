<?php

namespace App\Form;

use App\Entity\Params;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ParamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logo')
            ->add('slogan')
            ->add('nomApp')
            ->add('version')
            ->add('adresse')
            ->add('telephone')
            ->add('email')
            ->add('imageName' ,TextType::class, ['mapped' => false])
            ->add('imageName2' ,TextType::class, ['mapped' => false])
            ->add('imageSize')
            ->add('imageSize2')
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
            ->add('imageFile2', FileType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Params::class,
        ]);
    }
}
