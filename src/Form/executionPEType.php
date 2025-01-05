<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\ProgrammeEmploi;
use App\Entity\ExecutionPE;
use Doctrine\ORM\EntityRepository;

class executionPEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder 

/*             ->add('programme' , EntityType::class, array(
                    'class' => ProgrammeEmploi::class,
                    'placeholder' => '------Selectionner Programme------',
                    'query_builder' => function (EntityRepository $er) use($options){
                    return $er->createQueryBuilder('p')
                            ->leftJoin('p.personne', 'per')
                            ->addSelect('per')
                            ->andwhere('per.id = :personne')
                            ->setParameter('personne',$options['label']);
                  },
                  'choice_label' => 'intitule'
                )) */
                //element
                
            ->add('executionElements', CollectionType::class, [ 
                  'entry_type' => executionElementType::class,  
                  'entry_options' => [
                    'label' => $options['trim'],
                    'required' => false
                ],
                'by_reference' => false,
                'allow_add' =>true,
                'allow_delete' =>true, 
                ]);
                
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ExecutionPE',

        ));
    }

    public function getName()
    {
        return 'executionPEtype';
    }
}
