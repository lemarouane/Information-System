<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Etudiant\Absence;

use Symfony\Component\OptionsResolver\OptionsResolver;

class absenceImportType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
 
        $builder
            ->add('datedebut',DateType::class, array('widget' => 'single_text',
                                                    'required'=>true,
                                                    'html5' => false))
            ->add('datefin',DateType::class, array('widget' => 'single_text',
                                                    'required'=>true,
                                                    'html5' => false))

            ->add('codeapgeedebut',TextType::class)

            ->add('fichier',FileType::class, array(
                  'label' => 'choisissez votre fichier',
                  'data_class' => null,
                  'required' => false,
            ));
       
     }

   
    


     public function configureOptions(OptionsResolver $resolver): void
     {
         $resolver->setDefaults([
             'data_class' => Absence::class,
         ]);
     }

    public function getName()
    {
        return 'absenceImporttype';
    }
}