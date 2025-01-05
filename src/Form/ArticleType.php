<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;
use App\Entity\SousCategorie;
use App\Entity\BudgetPourcentage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ArticleType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            /* ->add('code',TextType::class, array('label' => 'Code:',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Code' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            )) */
            ->add('designation',TextType::class, array('label' => 'designation',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('reference',TextType::class, array('label' => 'ref',
                'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' ,
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('fournisseur',EntityType::class, array('label' => 'fournisseur',
            'class' => Fournisseur::class,
            'placeholder' => '------------',
            'choice_label' => function ($fournisseur) {
                    return $fournisseur->getRaisonSociale();
                },
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => '' ,
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            ))
            ->add('inv', ChoiceType::class, [
                'choices'  => [
                    'Inventaire' => true,
                    'Consomable' => false,
                ],
                'attr' => array(
                    'class' => 'form-select',
                  ),
                  'label_attr' => array(
                    'class' =>'form-label'
                  ),
            ])
            ->add('seuil',IntegerType::class, array( 'label' => 'seuil' , 
             'attr' => array(
                'class' => 'form-control',
                'placeholder' => '' , 
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                
             
          
                )))
            
            ;    
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));        
    }

    protected function addElements(FormInterface $form,  Categorie $categorie = null) {
      // 4. Add the province element
      
      $form->add('categorie',EntityType::class, array('label' => 'categorie',
                'class' => Categorie::class,
                'placeholder' => '------------',
                'choice_label' => function ($categorie) {
                        return $categorie->getDesignation();
                    },
                'attr' => array(
                  'class' => 'form-select',
                  'placeholder' => '' ,
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

      ));
      
      // Neighborhoods empty, unless there is a selected City (Edit View)
      $souscategorie = array();
      // If there is a city stored in the Person entity, load the neighborhoods of it
      if ($categorie) {
          // Fetch Neighborhoods of the City if there's a selected city

          $souscategorie = $this->em->getRepository(SousCategorie::class);
          
          $souscategorie = $souscategorie->createQueryBuilder("sc")
              ->where("sc.categorie = :categorieId")
              ->setParameter("categorieId", $categorie->getId())
              ->getQuery()
              ->getResult();
      }
       // If there is a city stored in the Person entity, load the neighborhoods of it
      
      
      // Add the Neighborhoods field with the properly data
      $form->add('souscategorie', EntityType::class, array(
            'required' => true,
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => '' ,
            ),
            'placeholder' => '------------',
            'class' => SousCategorie::class,
            'choice_label' => 'designation',
            'choices' => $souscategorie,
            'label_attr' => array(
              'class' =>'form-label'
            ),
          ));
      }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $categorie = $this->em->getRepository(Categorie::class)->find($data['categorie']);


        $this->addElements($form,  $categorie);
    }

    function onPreSetData(FormEvent $event) {
        $article = $event->getData();
        $form = $event->getForm();
        // When you create a new person, the City is always empty
        $categorie = $article->getCategorie() ? $article->getCategorie() : null;


        $this->addElements($form, $categorie);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Article',

        ));
    }

    public function getName()
    {
        return 'articletype';
    }
}
