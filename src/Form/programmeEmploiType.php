<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\programmeElementType;
use App\Entity\Personnel;
use App\Entity\ArticlePE;
use App\Entity\FiliereFcResponsable;
use App\Entity\Paragraphe;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\FormInterface;

class programmeEmploiType extends AbstractType
{

    private $conn;

    public function __construct( EntityManagerInterface $entityManager) {

        $this->em = $entityManager;

        $config = new \Doctrine\DBAL\Configuration();
		$connectionParams = array('url' => $_ENV['APOGEE_DATABASE_URL'].'',);
		$conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $this->conn = $conn;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

  

           $builder 
             ->add('personne' , EntityType::class, array(
                    'class' => Personnel::class,
                    'placeholder' => '------------',
                    'disabled' => true,
            
                    'choice_label' => function ($personne) {
                        return $personne->getNom().' '.$personne->getPrenom();
                    }))


            ->add('intitule' ,TextType::class,array( 'disabled' => true))
           // ->add('reference',TextType::class,array())
            ->add('type',ChoiceType::class, array(
                'choices' => array('Recette Propre' => '1'),
                'multiple' => false,
                'disabled' => true,
  
                'label' => 'Type:',
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

           /*  ->add('montant',NumberType::class, array('label' => 'Montant:',
                    'scale' => 1,
          
                    'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Montant' ,
                    'min' => 0 ,
                    'step' => '.1',
                    
                    ),
                    'label_attr' => array(
                    'class' =>'control-label'
                    ),
                    'row_attr' => [
                        'class' => 'col-sm-6'
                    ],
             )) */

            ->add('element', CollectionType::class, [ 
                  'entry_type' => programmeElementType::class,  
                  'entry_options' => [
                    'label' => $options['label'],
                    'help' => $options['help'],
                    'required' => false
                ],
                'by_reference' => false,
                'allow_add' =>true,
                'allow_delete' =>true,
                ])



                ->add('lien1', FileType::class, [
                    'label' => 'note_presentation',
                    'mapped' => false,
                 //   'required' => true,
              

                ])

                ->add('lien2', FileType::class, [
                    'label' => 'acreditation',
                    'mapped' => false,
                //    'required' => true,
                 
            
                ]);


                $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
                $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
                
    }
    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

         if(!empty($data['articlePE'])){

         
            // Search for selected City and convert it into an Entity
            $article = $this->em->getRepository(ArticlePE::class)->find($data['articlePE']);


            $this->addElements($form, $article);
        }

    }

    function onPreSetData(FormEvent $event) {
        $programe_emp = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
        $article = $programe_emp->getArticlePE() ? $programe_emp->getArticlePE() : null;


        $this->addElements($form, $article);
    }

    protected function addElements(FormInterface $form, ArticlePE $article = null ) {
        // 4. Add the province element
        $form->add('articlePE' , EntityType::class, array(
            'class' => ArticlePE::class,
            'mapped' => true,
            'disabled' => true,
            'placeholder' => '------------',
            'choice_label' => function ($articlePE) {
                return $articlePE->getNumArticle()." - ". $articlePE->getLibelle();
            }));
        
        // Neighborhoods empty, unless there is a selected City (Edit View)
        $paragraphe = array();
        // If there is a city stored in the Person entity, load the neighborhoods of it
        if ($article) {
            // Fetch Neighborhoods of the City if there's a selected city

            $paragraphe = $this->em->getRepository(Paragraphe::class);
            
            $paragraphe = $paragraphe->createQueryBuilder("p")
                ->where("p.articlePE = :articlePE")
                ->setParameter("articlePE", $article->getId())
                ->getQuery()
                ->getResult();
        }


        // Add the Neighborhoods field with the properly data
        $form->add('paragraphe', EntityType::class, array(
            'label'=>'Paragraphes',
            'disabled' => true,
            'required' => true,
            'placeholder' => '----------------------------',
            'class' => Paragraphe::class,
            'choices' => $paragraphe,
            'choice_label' => function ($p) {
                return  $p->getArticlePE()->getNumArticle()."-". $p->getNumParagraphe()."-". $p->getLibelle();
            }
            ));



       
     
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ProgrammeEmploi',

        ));
    }

    public function getName()
    {
        return 'programmeEmploitype';
    }
}
