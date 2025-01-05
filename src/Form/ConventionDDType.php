<?php

namespace App\Form;

use App\Entity\Etudiant\ConventionDD;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ConventionDDType extends AbstractType
{
    private $conn;

    public function __construct() {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $this->conn = $conn;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        $filieres = $this->conn->fetchAllAssociative("select *
                                                    from filiere
                                                    ");
        foreach ($filieres as $choice) {
          $choices[$choice['nom_filiere']] = $choice['code_apo'];
        }
        $builder
            ->add('etablissement',TextType::class, ['label' => 'Etablissement', 'label_attr' => ['class' => 'form-label']])
            ->add('fichier', FileType::class, [
                'label' => 'Invitation',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File()
                ],
            ])
            ->add('datedebut',DateType::class, array('widget' => 'single_text','required'=>true,
                                                      'html5' => false,
                                                      'attr' => ['class' => 'result form-control js-dateDebutConvention'],
                                                      'label_attr' => array(
                                                        'class' =>'form-label'
                                                        ),))
            ->add('datefin',   DateType::class, array('widget' => 'single_text','required'=>true,
                                                      'html5' => false,
                                                      'attr' => ['class' => ' result form-control js-dateFinConvention'],
                                                      'label_attr' => array(
                                                        'class' =>'form-label'
                                                    ),))
            ->add('contactEnsa',TextType::class, ['label' => 'Contact ENSAT', 'label_attr' => ['class' => 'form-label']])
            ->add('contactEtab',TextType::class, ['label' => 'Contact Etablissement', 'label_attr' => ['class' => 'form-label']])
            ->add('email',TextType::class, ['label' => 'E-mail', 'label_attr' => ['class' => 'form-label']])
            ->add('phone',TextType::class, ['label' => 'N° Téléphone', 'label_attr' => ['class' => 'form-label']])
            ->add('ville',TextType::class, ['label' => 'Ville', 'label_attr' => ['class' => 'form-label']])
            ->add('pays',TextType::class, ['label' => 'Pays', 'label_attr' => ['class' => 'form-label']])
            ->add('filiere',ChoiceType::class, array(
                'choices' => $choices,
            'label_attr' => [
                'class' => 'form-label',
            ],
            'required' => true,
            'expanded' => false,
            'attr' => ['class' => '' , 'style'=>'height:150px;' ],
            'multiple' => true,
            'placeholder' => '--Select Filière--',
            'label' => 'Filières'
        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConventionDD::class,
        ]);
    }
}
