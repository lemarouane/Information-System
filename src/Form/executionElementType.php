<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Rubrique;
use App\Entity\ExecutionElement;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
class executionElementType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

          $builder

        ->add('rubrique',EntityType::class, array('label' => 'Rubrique:',
              'class' => Rubrique::class,
              'placeholder' => '------Selectionner Rubrique------',
              'query_builder' => function (EntityRepository $er) use($options){


                  

                      return $er->createQueryBuilder('r')
                            ->leftJoin('r.element', 'e')
                            ->addSelect('e') 

                            ->leftJoin('e.programme', 'pe') 
                            ->addSelect('pe')
                      
                            ->andwhere('pe.id =:prog')
                             
                            ->setParameter('prog', intval($options['label']) ) 

                             ->andwhere('pe.montant is not NULL ')
                             ->andwhere('pe.montant != 0') ;
                            
                  }
                  
                  
                  
                  ,
                'choice_label' => 'libelle', 
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
        ->add('intitule',TextType::class, array('label' => 'Intitule:',
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

        ->add('description',TextareaType::class, array('label' => 'Description:',
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


       /*    ->add('montant',IntegerType::class, array('label' => 'Montant:',
                'attr' => array(
                  'class' => 'form-control',
                  'min' => 0 
                ),
                'label_attr' => array(
                  'class' =>'control-label'
                ),
                'row_attr' => [
                    'class' => 'col-sm-6'
                ],


          )) */
          
          
          ;
        
        
     
        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ExecutionElement::class,

        ));
      
    }


    public function getName()
    {
        return 'executionElementType';
    }

}
