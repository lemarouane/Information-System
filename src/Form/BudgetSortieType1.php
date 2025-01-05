<?php

namespace App\Form;

use App\Entity\BudgetSortie;
use App\Entity\Budget;
use App\Entity\StructRech;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;

class BudgetSortieType1 extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
  
            ->add('budget',EntityType::class, array('label' => 'budget',
                'class' => Budget::class,
                'placeholder' => '------------',
                'choice_label' => function ($budget) {
                        return $budget->getLibelle();
                    },
                    'attr' => array(
                        'class' => 'form-select',
                        'placeholder' => '-----------' ,
                    ),
                    'label_attr' => array(
                        'class' =>'form-label'
                    ),
                    'row_attr' => [
                        'class' => 'col-6 '
                    ],
              ))

            ->add('libelle',TextType::class, array('label' => 'libelle',
                  'attr' => array(
                  'class' => 'form-control',
                  ),
                  'label_attr' => array(
                      'class' => 'form-label',
                  ),
                  'row_attr' => [
                    'class' => 'col-6 '
                ],
              ))


            ->add('montant',IntegerType::class, array('label' => 'montant',
                  'attr' => array(
                  'class' => 'form-control',
                    ),
                  'label_attr' => array(
                  'class' => 'form-label',
                  ),
                  'row_attr' => [
                    'class' => 'col-6 '
                ],
            ))

 


        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));   
    }


    protected function addElements(FormInterface $form , $type = null) {
        // 4. Add the province element
        $form->add('type_structure',ChoiceType::class, array('label' => 'Type:',

                  'choices'  => [
                    'Département' => 1,
                    'Laboratoire / Equipe de Recherche' => 2,
                ],
                  'attr' => array(
                    'class' => 'form-select typeStruct',
                  ),
                  'label_attr' => array(
                    'class' =>'form-label'
                  ),
                  'row_attr' => [
                    'class' => 'col-6 '
                ],
  
        ));
        
        // Neighborhoods empty, unless there is a selected City (Edit View)
          $structure = array();
          
        $choices = [];
        
        // If there is a city stored in the Person entity, load the neighborhoods of it
        if ($type == 1  ) {
            // Fetch Neighborhoods of the City if there's a selected city
  
            $structure = $this->em->getRepository(Departement::class);
            $structure = $structure->findAll();

            foreach ($structure as $choice) {
              $choices[$choice->getLibelleDep()] = $choice->getId();
            }
            
        }
       else{
            // Fetch Neighborhoods of the City if there's a selected city
  
            $structure = $this->em->getRepository(StructRech::class);
            $structure = $structure->findAll();

            foreach ($structure as $choice) {
              $choices[$choice->getLibelleStructure()] = $choice->getId();
            }
           
        }
        $form->add('structure', ChoiceType::class, array(
          'required' => true,
          'attr' => array(
            'class' => 'form-select',
          ),
          'placeholder' => '------Selectionner Structure------',

          'choices' => $choices,
          'label_attr' => array(
            'class' =>'form-label'
          ),
          'row_attr' => [
            'class' => 'col-6 '
        ],
      ));
         // If there is a city stored in the Person entity, load the neighborhoods of it
        
        
        // Add the Neighborhoods field with the properly data
               
            
            ;
        }

        function onPreSubmit(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
    
            // Search for selected City and convert it into an Entity
            $type = $data['type_structure'];
    
    
            $this->addElements($form, $type);
        }
    
        function onPreSetData(FormEvent $event) {
            $structure = $event->getData();
            $form = $event->getForm();
            // When you create a new person, the City is always empty
            // $structure =  $structure->getArticlePE() ? $ligne->getArticlePE() : null;
    
    
            $this->addElements($form, 1);
        }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BudgetSortie::class,
        ]);
    }
}
