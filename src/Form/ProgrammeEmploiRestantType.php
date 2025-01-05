<?php

namespace App\Form;

use App\Entity\ProgrammeEmploiRestant;
use App\Entity\ArticlePE;
use App\Entity\ProgrammeElementRestant; 
use App\Form\programmeElementRestantType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class ProgrammeEmploiRestantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

  
    ->add('intitule' ,TextType::class,array( 'disabled' => false))
   // ->add('reference',TextType::class,array())
    

    ->add('montant',NumberType::class, array('label' => 'Montant:',
            'scale' => 1,
  
            'attr' => array(
            'class' => 'form-control',
            'min' => 0 ,
            'step' => '.1',
            
            ),
            'label_attr' => array(
            'class' =>'control-label'
            ),
            'row_attr' => [
                'class' => 'col-sm-6'
            ],
     )) 

    ->add('programmeElementRestants', CollectionType::class, [ 
          'entry_type' => programmeElementRestantType::class,  
          'entry_options' => [
            'label' => $options['label'],
            'help' => $options['help'],
            'required' => false
        ],
        'by_reference' => false,
        'allow_add' =>true,
        'allow_delete' =>true,
        ])

     //   $query->andWhere('s.filiere like :keyword OR s.filiere like :key1 OR s.filiere like :key2 ')->setParameter('keyword',$filiere.'%')->setParameter('key1', 'IISI%')

            ->add('annee')

            ->add('articlePE' , EntityType::class, array(
                'class' => ArticlePE::class,
                'mapped' => true,
                'disabled' => false,
                'placeholder' => '------------',

                'query_builder' => function (EntityRepository $er) use($options){
              
                      $articlePE = $er->createQueryBuilder('a')
                      ->addSelect('a')
                      ->andWhere('a.numArticle like :k1 OR a.numArticle like :k2')->setParameter('k1','909')->setParameter('k2', '920');
              
                        return $articlePE;
                      },
                
                'choice_label' => function ($articlePE) {

                    return $articlePE->getNumArticle()." - ". $articlePE->getLibelle();

                }))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProgrammeEmploiRestant::class,
        ]);
    }
}
