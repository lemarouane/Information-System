<?php

namespace App\Form;

use App\Entity\OrdreMission;
use App\Entity\Engagement;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\EngagementType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Security\Core\Security as secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class OrdreMissionType extends AbstractType
{
    private $order_mission_type = [] ; 
    public function __construct(secure $security) 
    {
    if(in_array("ROLE_FONC",$security->getUser()->getRoles()) || in_array("ROLE_RH",$security->getUser()->getRoles())
     || in_array("ROLE_CHEF_SERV",$security->getUser()->getRoles()) || in_array("ROLE_SG",$security->getUser()->getRoles()) 
     || in_array("ROLE_SCOLARITE",$security->getUser()->getRoles()) ||  in_array("ROLE_SERVICEEXT",$security->getUser()->getRoles()) ){

    $this->order_mission_type = ['deplac' => 'D'] ;
          }else{
    $this->order_mission_type =  ['rech' => 'R','ens' => 'E','deplac' => 'D'] ;
          }
                 
    }
    


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('typeMission', ChoiceType::class, [
           'label' => 'type',
            'placeholder' => '------------',
                'choices'  => $this->order_mission_type,
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'required' => true ,
                
            ])

            ->add('moyenTransport', ChoiceType::class, [
                'label' => 'moy_transp',
                'placeholder' => '------------',
                    'choices'  => [
                        'transp_pub' => 'Transport Public',
                        'voit_perso' => 'Voiture Personnelle',  
                        'avion' => 'Avion',  
                    ],
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'required' => true ,
                    
                ])
 
            ->add('cadreMission' ,TextType::class, ['label' => 'cadre_mission', 'label_attr' => ['class' => 'form-label']])
            ->add('objetMission' ,TextType::class, [ 'label' => 'objet','label_attr' => ['class' => 'form-label']])
            ->add('motif' ,TextType::class, [ 'label' => 'motif','label_attr' => ['class' => 'form-label']])
            ->add('valeurAutre' ,TextType::class, [ 'label' => 'val_autre','label_attr' => ['class' => 'form-label'] , 'required' => false ])
            ->add('valeurProjet' ,TextType::class, [ 'label' => 'val_projet','label_attr' => ['class' => 'form-label'] , 'required' => false])

            ->add('valeurfc' ,TextType::class, [ 'label' => 'val_fc','label_attr' => ['class' => 'form-label'] , 'required' => false])

            ->add('valeurautrevg' ,TextType::class, [ 'label' => 'val_autre_vg','label_attr' => ['class' => 'form-label'] , 'required' => false ])
            ->add('valeurprojetvg' ,TextType::class, [ 'label' => 'val_projet_vg','label_attr' => ['class' => 'form-label'] , 'required' => false])
            ->add('valeurfcvg' ,TextType::class, [ 'label' => 'val_fc_vg','label_attr' => ['class' => 'form-label'] , 'required' => false])

            ->add('destination' ,TextType::class, [ 'label' => 'destination','label_attr' => ['class' => 'form-label']])
            ->add('structureAcceuil' ,TextType::class, ['label' => 'str_acceuil', 'label_attr' => ['class' => 'form-label']])
            
            ->add('marqueauto' ,TextType::class, ['label' => 'marqueauto', 'label_attr' => ['class' => 'form-label']  ])
            ->add('matriculeauto' ,TextType::class, ['label' => 'matriculeauto', 'label_attr' => ['class' => 'form-label'] ])
  

            ->add('typedest', ChoiceType::class, [
                'label' => 'typedest',
                'placeholder' => '------------',
                    'choices'  => [
                        'nationale' => 'nationale',
                        'etrangere' => "etrangere",  
                    ],
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'required' => true ,
                    
                ])
        
        
        
            ->add('dateDebut',DateType::class, array('label' => 'date_debut','widget' => 'single_text' ,  
            'required' => true , 
            'attr' => ['class' => 'result form-control js-dateDebutOrderMission'],
            'label_attr' => ['class' => 'form-label'],
            'html5' => false,
            ))
            ->add('dateFin',DateType::class, array('label' => 'date_fin','widget' => 'single_text' ,
            'required' => true ,
            'label_attr' => ['class' => 'form-label'],
            'attr' => ['class' => 'result form-control js-dateFinOrderMission'],
            'html5' => false,
                ))
    
           ->add('financementMission',ChoiceType::class,array(
                  'translation_domain' => 'messages', 
                   'label' => 'financement',
                    'choices' => array(
                        'Laboratoire' => 'Laboratoire',
                        'Departement / Administration' => 'Departement',
                        'Projet'      => 'Projet',
                        'Autre'       => 'Autre',
                        'Formation Continue'   => 'FC',
                        'Sans frais'  => 'Sans frais',
                   
                    ),
    
                    'choice_attr' => function($val, $key, $index) {
                        // adds a class like attending_yes, attending_no, etc
                        return ['class' => 'form-check-input'];
                    },
                    'expanded' => true,
                    'multiple' => true,
                    'required' => true,
                    'placeholder' => false,
                ))

                ->add('financementvoyage',ChoiceType::class,array(
                    'translation_domain' => 'messages', 
                     'label' => 'financement_voyage',
                      'choices' => array(
                        'Laboratoire' => 'Laboratoire',
                        'Departement / Administration' => 'Departement',
                        'Projet'      => 'Projet',
                        'Autre'       => 'Autre',
                        'Formation Continue'   => 'FC',
                        'Sans frais'  => 'Sans frais',
                     
                      ),
      
                      'choice_attr' => function($val, $key, $index) {
                          // adds a class like attending_yes, attending_no, etc
                          return ['class' => 'form-check-input'];
                      },
                      'expanded' => true,
                      'multiple' => true,
                      'required' => true,
                      'placeholder' => false,
                  ))
  

            ->add('invitFile', FileType::class, [
                'label' => 'Invit',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
            
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File()
                ],
            ])
        
        
            ->add('engagements', CollectionType::class, [ 
                'label' => 'engagements',
                'entry_type' => EngagementType::class,  
                'entry_options' => [
                  'label' => false,
                  'required' => false
              ],
              'by_reference' => false,
              'allow_add' =>true,
              'allow_delete' =>true,
              ])
    

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrdreMission::class,
        ]);
    }
}
