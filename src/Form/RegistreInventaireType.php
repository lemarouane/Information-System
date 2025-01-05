<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;
use App\Entity\Personnel;
use App\Entity\ReceptionLigne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class RegistreInventaireType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
            ->add('numinventaire',TextType::class, array('label' => 'n_inventaire',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('datereception',DateType::class, array('label' => 'date_reception','widget' => 'single_text' ,  
            'required' => true , 
            'attr' => ['class' => 'result form-control js-dateReception'],
            'label_attr' => ['class' => 'form-label'],
            'html5' => false,
            ))
            ->add('numbcao',TextType::class, array('label' => 'n_bc_ao',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('numlivraison',TextType::class, array('label' => 'n_livraison',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('raisonsocialefournisseur',TextType::class, array('label' => 'founisseur',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                )
            ))
            ->add('etatconservation', ChoiceType::class, [
                'label' => 'etat',
                'choices'  => [
                    'neuf' => 'neuf',
                    'seconde_main' => 'seconde main',
                ],
                'attr' => array('class' => 'form-select')
            ])
            ->add('local',TextType::class, array('label' => 'locale',
                'attr' => array(
                'class' => 'form-control',
                ),
                'label_attr' => array(
                    'class' => 'form-label',
                ),
                'required' => false,
            ))
            ->add('remarque',TextType::class, array('label' => 'remarque',
                'attr' => array(
                'class' => 'form-control',

                ),
                'label_attr' => array(
                    'class' => 'form-label',
                ),
                'required' => false,
            ))
                        
            ;    
            $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
            $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));        
    }

    protected function addElements(FormInterface $form, ReceptionLigne $receptionligne = null, Personnel $personnel = null) {
      // 4. Add the province element
      $form->add('receptionligne',EntityType::class, array('label' => 'Ligne de reception:',
                'class' => ReceptionLigne::class,
                'placeholder' => '------------',
                'choice_label' => function ($receptionligne) {
                        return $receptionligne->getReception()->getNumReception().' '.$receptionligne->getArticle()->getCode();
                    },
                'attr' => array(
                  'class' => 'form-select',
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),

      ))
      ->add('personnel',EntityType::class, array('label' => 'nom_prenom',
                'class' => Personnel::class,
                'placeholder' => '------------',
                'choice_label' => function ($personnel) {
                        return $personnel->getNom().' '.$personnel->getPrenom();
                    },
                'attr' => array(
                  'class' => 'form-select',
                ),
                'label_attr' => array(
                  'class' =>'form-label'
                ),
                'required' => false,

      ));
      
      
      }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected City and convert it into an Entity
        $receptionligne = $this->em->getRepository(ReceptionLigne::class)->find($data['receptionligne']);
        $personnel = $this->em->getRepository(Personnel::class)->find($data['personnel']);

        $this->addElements($form, $receptionligne , $personnel);
    }

    function onPreSetData(FormEvent $event) {
        $inventaire = $event->getData();
        $form = $event->getForm();
        // When you create a new person, the City is always empty
        $receptionligne = $inventaire->getReceptionLigne() ? $inventaire->getReceptionLigne() : null;
        $personnel = $inventaire->getPersonnel() ? $inventaire->getPersonnel() : null;

        $this->addElements($form, $receptionligne , $personnel);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\RegistreInventaire',

        ));
    }

    public function getName()
    {
        return 'RegistreInventaireType';
    }
}
