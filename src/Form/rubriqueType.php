<?php

namespace App\Form;

use App\Entity\ArticlePE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Ligne;
use App\Entity\BudgetPourcentage;
use App\Entity\Paragraphe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
class rubriqueType extends AbstractType
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
            ->add('numRubrique',IntegerType::class, array('label' => 'N° Rubrique:',
                'attr' => array(
                  'class' => 'form-control',
                  'placeholder' => 'N° Rubrique' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),



            ))
            

            ->add('type',ChoiceType::class, array(
                'choices' => array('Subvention d\'Etat' => '1', 'Recette Propre' => '2', 'Les Deux' => '3'),
                'multiple' => false,
                'label' => 'Type:',
                'attr' => array(
                  'class' => 'form-control',  
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

                
            ))
            ->add('affichage',ChoiceType::class, array(
                'choices' => array('OUI' => 'OUI', 'NON' => 'NON'),
                'multiple' => false,
                'label' => 'Affichage:',
                'attr' => array(
                  'class' => 'form-control',  
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

                
            ))
            ;   
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));            
    }

    protected function addElements(FormInterface $form, ArticlePE $articlePE = null, Paragraphe $paragraphes = null) {
      // 4. Add the province element
        $form->add('articlePE',EntityType::class, array('label' => 'Article:',
                'class' => ArticlePE::class,
                'placeholder' => '------Selectionner Article------',
                'choice_label' => function ($articlePE) {
                        return $articlePE->getLibelle();
                    },
                'attr' => array(
                  'class' => 'form-control',
                  'placeholder' => 'Article' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
        ));
      
        // Neighborhoods empty, unless there is a selected City (Edit View)
        $paragraphe = array();
        $ligne = array();
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
        if ($paragraphes) {
          // Fetch Neighborhoods of the City if there's a selected city

          $ligne = $this->em->getRepository(Ligne::class);
              
          $ligne = $ligne->createQueryBuilder("l")
              ->where("l.paragraphe = :paragrapheid")
              ->setParameter("paragrapheid", $paragraphes->getId())
              ->getQuery()
              ->getResult(); 
                  

            
        }

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

          // Add the Neighborhoods field with the properly data
          $form->add('ligne', EntityType::class, array(
            'required' => true,
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => 'Ligne' ,
            ),
            'placeholder' => '------Selectionner Ligne------',
            'class' => Ligne::class,
            'choice_label' => 'libelle',
            'choices' => $ligne,
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
        $paragraphe = $this->em->getRepository(Paragraphe::class)->find($data['paragraphe']);


        $this->addElements($form, $articlePE,$paragraphe);
    }

    function onPreSetData(FormEvent $event) {
        $rubrique = $event->getData();
        $form = $event->getForm();
        // When you create a new person, the City is always empty
        $articlePE = $rubrique->getArticlePE() ? $rubrique->getArticlePE() : null;
        $paragraphe = $rubrique->getParagraphe() ? $rubrique->getParagraphe() : null;


        $this->addElements($form, $articlePE,$paragraphe);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Rubrique',

        ));
    }

    public function getName()
    {
        return 'rubriquetype';
    }
}
