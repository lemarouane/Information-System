<?php

namespace App\Form;

use App\Entity\Etudiant\ConventionDD;
use App\Entity\Etudiant\EtudiantDD;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class EtudiantDDType extends AbstractType
{
    private $conn;

    public function __construct() {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $this->conn = $conn;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        $choices1 = [];
        $annesS = [] ;
        $anne= date('Y');
        $annedebut = intval($anne)-3;
        for ($i=$annedebut ; $i < intval($anne) + 8; $i++) { 
            $annesS[$i.'/'.($i+1)] = $i;
        }

        $query = "SELECT DISTINCT(ETP.COD_ETP) as COD_ETP FROM ins_pedagogi_etp ETP WHERE (ETP.COD_ETP LIKE 'II%' OR ETP.COD_ETP LIKE 'IM%') ORDER BY ETP.COD_ETP ASC";
       /*  foreach($options['label_attr'] as $fil){
                
            $query .= "  ETP.COD_ETP LIKE '".$fil."%' OR";
                
        }
        
        $query=substr($query, 0, -2);
        $query .=" ) ORDER BY ETP.COD_ETP ASC"; */

        $filieres = $this->conn->fetchAllAssociative($query);
        foreach ($filieres as $choice) {
          $choices[$choice['COD_ETP']] = $choice['COD_ETP'];
        }
        foreach ($options['label_format'] as $convention) {
            $choices1[$convention['etablissement']] = $convention['id'];
          }
        $builder
            ->add('anneeSoutenance',ChoiceType::class, array(
                'choices' => $annesS,
                'label_attr' => [
                'class' => 'form-label',
            ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'mapped'   => false,
                'placeholder' => '----',
                'label' => 'annee_prevu_soutenance'
            ))
            ->add('filiere',ChoiceType::class, array(
                'choices' => $choices,
                'label_attr' => [
                'class' => 'form-label',
            ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'mapped'   => false,
                'placeholder' => '----',
                'label' => 'filiere'
            ))
            ->add('convention',ChoiceType::class, array(
                'choices' => $choices1,
                'label_attr' => [
                'class' => 'form-label',
            ],
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'mapped'   => false,
                'placeholder' => '----',
                'label' => 'conventions'
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EtudiantDD::class,
        ]);
    }
    public function getDefaultOptions(array $options)
    {
        return array(
            'label' => false,
            'filiere' => false,
            'convention' => false,
        );
    }
}
