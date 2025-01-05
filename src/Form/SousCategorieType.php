<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class SousCategorieType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('code',TextType::class, array('label' => 'code',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('designation',TextType::class, array('label' => 'designation',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))

            
            ;    
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));        
    }

    protected function addElements(FormInterface $form, Categorie $categorie = null) {
      // 4. Add the province element
      $form->add('categorie',EntityType::class, array('label' => 'Categorie:',
                'class' => Categorie::class,
                'placeholder' => '------------',
                'choice_label' => function ($categorie) {
                        return $categorie->getDesignation();
                    },
                'attr' => array(
                  'class' => 'form-select',
                  'placeholder' => 'Categorie' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

      ));
      
      // Neighborhoods empty, unless there is a selected City (Edit View)
/*       $paragraphe = array();
      // If there is a city stored in the Person entity, load the neighborhoods of it
      if ($articlePE) {
          // Fetch Neighborhoods of the City if there's a selected city

          $paragraphe = $this->em->getRepository(Paragraphe::class);
          
          $paragraphe = $paragraphe->createQueryBuilder("p")
              ->where("p.articlePE = :articlePEId")
              ->setParameter("articlePEId", $articlePE->getId())
              ->getQuery()
              ->getResult();
      } 
       // If there is a city stored in the Person entity, load the neighborhoods of it
      
      
      // Add the Neighborhoods field with the properly data
      $form->add('paragraphe', EntityType::class, array(
            'required' => true,
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => 'Paragraphe' ,
            ),
            'placeholder' => '------Selectionner Paragraphe------',
            'class' => Paragraphe::class,
            'choice_label' => 'libelle',
            'choices' => $paragraphe,
            'label_attr' => array(
              'class' =>'form-label'
            ),
          ));*/
      }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $categorie = $this->em->getRepository(Categorie::class)->find($data['categorie']);


        $this->addElements($form, $categorie);
    }

    function onPreSetData(FormEvent $event) {
        $souscategorie = $event->getData();
        $form = $event->getForm();
        // When you create a new person, the City is always empty
        $categorie = $souscategorie->getCategorie() ? $souscategorie->getCategorie() : null;


        $this->addElements($form, $categorie);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\SousCategorie',

        ));
    }

    public function getName()
    {
        return 'SousCategorietype';
    }
}
