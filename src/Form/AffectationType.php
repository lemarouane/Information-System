<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\RegistreInventaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Personnel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AffectationType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 

             ->add('article',EntityType::class, array('label' => 'consommable',
            'class' => Article::class,
            'placeholder' => '------------',
            'choice_label' => function ($article) {
                if($article->isinv())
                return ;
                else
                    return $article->getDesignation().' '.$article->getReference();
                },
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => '' ,
              'disabled' => true,
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            )) 
            ->add('personnel',EntityType::class, array('label' => 'personnel',
            'class' => Personnel::class,
            'placeholder' => '------------',
            'choice_label' => function ($personnel) {
                    return $personnel->getNom().' '.$personnel->getPrenom();
                },
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => '' ,
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            ))
             ->add('qte',IntegerType::class, array( 'label' => 'Quantite affecte:' , 
             'attr' => array(
                'class' => 'form-control',
                'placeholder' => 'Quantite' ,
                'min' => 1 
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                
             
          
                )))
                ->add('inventaire',EntityType::class, array('label' => 'inventaire',
            'class' => RegistreInventaire::class,
            'placeholder' => '------------',
            'choice_label' => function ($inv) {
                    return $inv->getNumInventaire();
                },
            'attr' => array(
              'class' => 'form-select',
              'placeholder' => 'inventaire' ,
            ),
            'label_attr' => array(
              'class' =>'form-label'
            ),
            ))  
            ;   

                
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            //$builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit')); 
    }

    protected function addElements(FormInterface $form,  Article $article = null) {
        // 4. Add the province element
        
        $form->add('qte',IntegerType::class, array( 'label' => 'Quantite affecte:' , 'attr' => array(
            'class' => 'form-control',
            'placeholder' => '--',
            'required' => true ,
            'min' =>1, 'max' =>$article?->getQte(),
      
            )));
        }
  
/*       function onPreSubmit(FormEvent $event) {
          $form = $event->getForm();
          $data = $event->getData();
  
          // Search for selected City and convert it into an Entity
          $article = $this->em->getRepository(Article::class)->find($data['article']);
  
  
          $this->addElements($form,  $article);
      } */
  
      function onPreSetData(FormEvent $event) {
          $affectation = $event->getData();
          $form = $event->getForm();
          // When you create a new person, the City is always empty
          $article = $affectation?->getArticle() ? $affectation?->getArticle() : null;
  
  
          $this->addElements($form, $article);
      }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Affectation',

        ));
    }

    public function getName()
    {
        return 'AffectationType';
    }
}
