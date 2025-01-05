<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Rubrique;
use App\Entity\ProgrammeEmploiBudget;
use App\Entity\ProgrammeElementBudget;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Doctrine\ORM\EntityRepository;
class ProgrammeElementBudgetType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

          $builder
          

        ->add('rubrique',EntityType::class, array('label' => 'Rubrique',
              'class' => Rubrique::class,
              'placeholder' => '------------',
              'query_builder' => function (EntityRepository $er) use($options){
                if($options['help']==null){

                  $rubrique = $er->createQueryBuilder('r')
                  ->addSelect('r')
                  ->leftJoin('r.articlePE', 'a')
                  ->addSelect('a')

                  ->andwhere('a.id = :art')
                  ->setParameter('art',$options['label'])
                  ->andwhere('r.affichage = :art1')
                  ->setParameter('art1','OUI');

                }else{
             
                  $rubrique = $er->createQueryBuilder('r')
                  ->addSelect('r')
                  ->leftJoin('r.articlePE', 'a')
                  ->addSelect('a')
                  ->leftJoin('r.paragraphe', 'p')
                  ->addSelect('p')
                  ->andwhere('a.id = :art')
                  ->andwhere('r.affichage = :art1')
                  ->setParameter('art',$options['label'])
                  ->setParameter('art1','OUI');

                }
               
                                            
                            return $rubrique;
                  },
              'choice_label' => function ($rubrique) {
                return $rubrique->getLigne()->getParagraphe()->getArticlePE()->getNumArticle()."-".$rubrique->getLigne()->getParagraphe()->getNumParagraphe()."-".$rubrique->getNumRubrique()."-".$rubrique->getLibelle() ."---".$rubrique->getLigne()->getType();
            },
                'attr' => array(
                  'class' => 'form-control', 
                ),
                'label_attr' => array(
                  'class' =>'control-label'
                ),
                'row_attr' => [
                    'class' => 'col-sm-6'
                ],


          ))
        ->add('montant',NumberType::class, array('label' => 'Montant',
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

        
        ;
     
        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProgrammeElementBudget::class
        ));
    }


    public function getName()
    {
        return 'programmeElementBudgetType';
    }

}
