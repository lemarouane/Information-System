<?php

namespace App\Form;

use App\Entity\ArticlePE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Paragraphe;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ligneType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('libelle',TextType::class, array('label' => 'Libelle:',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Libelle' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('numLigne',IntegerType::class, array('label' => 'N° Ligne:',
                'attr' => array(
                  'class' => 'form-control',
                  'placeholder' => 'N° Ligne' ,
                  'min' => 0 
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),



            ))
            ->add('type',ChoiceType::class, array(
                'choices' => array('Exploitation Personnel' => 'Exploitation Personnel', 'Exploitation MDD' => 'Exploitation MDD'),
                'multiple' => false,
                'label' => 'Type:',
                'attr' => array(
                  'class' => 'form-select',  
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
            ))

            
            ;    
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));        
    }

    protected function addElements(FormInterface $form, ArticlePE $articlePE = null) {
      // 4. Add the province element
      $form->add('articlePE',EntityType::class, array('label' => 'Article:',
                'class' => ArticlePE::class,
                'placeholder' => '------Selectionner Article------',
                'choice_label' => function ($articlePE) {
                        return $articlePE->getLibelle();
                    },
                'attr' => array(
                  'class' => 'form-select',
                  'placeholder' => 'Article' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

      ));
      
      // Neighborhoods empty, unless there is a selected City (Edit View)
      $paragraphe = array();
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
          ));
      }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $articlePE = $this->em->getRepository(ArticlePE::class)->find($data['articlePE']);


        $this->addElements($form, $articlePE);
    }

    function onPreSetData(FormEvent $event) {
        $ligne = $event->getData();
        $form = $event->getForm();
        // When you create a new person, the City is always empty
        $articlePE = $ligne->getArticlePE() ? $ligne->getArticlePE() : null;


        $this->addElements($form, $articlePE);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Ligne',

        ));
    }

    public function getName()
    {
        return 'lignetype';
    }
}
