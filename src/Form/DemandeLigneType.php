<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class DemandeLigneType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            
            ->add('article',EntityType::class, array('label' => 'article',
                'class' => Article::class,
                'placeholder' => '------------',
                'choice_label' => function ($article) {
                        return $article->getDesignation();
                    },
                'attr' => array(
                  'class' => 'form-select',
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
                'row_attr' => [
                  'class' => 'col-6 '
              ],

      ))

      ->add('qte',IntegerType::class, array('label' => 'qte',
                'attr' => array(
                  'class' => 'form-control',
                  'min' => 0 
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
                'row_attr' => [
                  'class' => 'col-6 '
              ],



            ))
      ;
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\DemandeLigne',

        ));
    }

    public function getName()
    {
        return 'demandelignetype';
    }
}
