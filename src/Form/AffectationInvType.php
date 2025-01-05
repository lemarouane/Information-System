<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\RegistreInventaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Personnel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AffectationInvType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
                ->add('inventaire',EntityType::class, array('label' => 'liste_inventaire_reception',
            'class' => RegistreInventaire::class,
            'placeholder' => '------------',
            'choice_label' => function ($inv) {
                    return $inv->getNumInventaire();
                },
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => '' ,
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            ))  
            ;   

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Affectation',

        ));
    }

    public function getName()
    {
        return 'AffectationInvType';
    }
}
