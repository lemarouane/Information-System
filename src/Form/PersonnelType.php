<?php

namespace App\Form;

use App\Entity\Personnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Corps;
use App\Entity\Grades;
use App\Entity\Fonction;
use App\Entity\Utilisateurs;
use App\Entity\Service;
use App\Entity\StructRech;
use App\Entity\SituationAdm;
use App\Entity\Province;
use App\Entity\Specialite;
use App\Entity\Departement;
use App\Entity\Diplome;
use App\Entity\TypePersonnel;
use App\Entity\Echelon;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;


class PersonnelType extends AbstractType
{

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
         
            ->add('genre', ChoiceType::class, [
                'translation_domain' => 'messages',
                'choices'  => ['Homme' => 'M','Femme' => 'F'],
                'label'=>'genre'
            ])
            ->add('numPPR',IntegerType::class, array('label'=>'numPPR'))
            ->add('nom',TextType::class, array('label'=>'nom'))
            ->add('prenom',TextType::class, array('label'=>'prenom'))
            ->add('etabDelivreDiplome',TextType::class, array('label'=>'etab_diplome'))
            ->add('tel',TextType::class, array('label'=>'tel'))
            ->add('nomArabe',TextType::class, array('label'=>'nom_ar'))
            ->add('prenomArabe',TextType::class, array('label'=>'prenom_ar'))
            ->add('cin',TextType::class, array('label'=>'cin'))
            ->add('posteBudget',TextType::class, array('label'=>'post_budget'))
            ->add('nbEnfant',IntegerType::class, array('label'=>'nb_enfant','required' => false))

            ->add('situationFamiliale',TextType::class, array('label'=>'sit_famil'))

            ->add('situationFamiliale', ChoiceType::class, [
                'label'=>'sit_famil',
                'placeholder' => '------------',
                                                'choices'  => [
                                                    'calib' => 'Célibataire',
                                                    'marie' => 'Marié(e)',
                                                    'divorce' => "Divorcé(e)",
                                                    'veuf' =>  "Veuf(ve)",
                                                ],
                                            ])


            ->add('adresse',TextType::class, array('label'=>'adresse','required' => false))
            ->add('dateNaissance',DateType::class, array('widget' => 'single_text','label'=>'date_n'))
            ->add('dateAffectationENSAT',DateType::class, array('widget' => 'single_text','label'=>'date_affect_ENSAT'))
            ->add('dateAffectationMESRSFC',DateType::class, array('widget' => 'single_text','label'=>'date_affect_minsup'))
            ->add('dateSoutenanceH',DateType::class, array('widget' => 'single_text','label'=>'date_sout','required' => false))
            ->add('datedeces',DateType::class, array('widget' => 'single_text','label'=>'date_deces','required' => false))
            ->add('dateAffectationEnseignement',DateType::class, array('widget' => 'single_text','label'=>'date_affect_ens' , 'required' => false))
            ->add('dateRecrutement',DateType::class, array('widget' => 'single_text','label'=>'date_rec'))
            ->add('email', TextType::class, array( 'mapped' => false,'label'=>'E-mail'))
            ->add('imageName' ,TextType::class, ['mapped' => false])
            ->add('imageFile', FileType::class, [
                   'label' => 'Profile Picture',
               
                   // unmapped means that this field is not associated to any entity property
                   'mapped' => false,
               
                   // make it optional so you don't have to re-upload the PDF file
                   // every time you edit the Product details
                   'required' => false,
               
                   // unmapped fields can't define their validation using annotations
                   // in the associated entity, so you can use the PHP constraint classes
                   'constraints' => [
                       new File([
                           'maxSize' => '8024k',
                           'mimeTypes' => [
                               'application/jpg',
                               'application/x-jpg',
                               'application/png',
                               'application/x-png',
                           ],
                           'mimeTypesMessage' => 'Please upload a valid JPG document',
                       ])
                   ],
               ])


            

            ->add('fonctionExercee' , EntityType::class, array(
                'class' => Fonction::class,
                'label'=>'fonct_exercee',
                'choice_label' => 'FonctionExercee',
                'mapped' => true,
                'placeholder' => '----------------------------',
                'data' => $options['data']->getFonctionExercee()))

            ->add('serviceAffectationId' , EntityType::class, array(
                    'class' => Service::class,
                    'label'=>'service_affct',
                    'choice_label' => 'nomService',
                    'mapped' => true,
                    'placeholder' => '----------------------------',
                    'data' => $options['data']->getServiceAffectationId()))

            ->add('structureRech' , EntityType::class, array(
                        'class' => StructRech::class,
                        'label'=>'str_rech',
                        'choice_label' => 'libelleStructure',
                        'mapped' => true,
                        'placeholder' => '----------------------------',
                        'data' => $options['data']->getStructureRech()))

            ->add('situationAdm' , EntityType::class, array(
                            'class' => SituationAdm::class,
                            'label'=>'sit_admin',
                            'choice_label' => 'libelleSituation',
                            'mapped' => true,
                            'placeholder' => '----------------------------',
                            'data' => $options['data']->getSituationAdm()))

            ->add('provinceNaissance' , EntityType::class, array(
                            'class' => Province::class,
                            'label'=>'provinces',
                            'choice_label' => 'nomProvince',
                            'mapped' => true,
                            'placeholder' => '----------------------------',
                            'data' => $options['data']->getProvinceNaissance()))


           

            ->add('diplomeId' , EntityType::class, array(
                                'class' => Diplome::class,
                                'label'=>'diplome',
                                'choice_label' => 'designationFR',
                                'mapped' => true,
                                'placeholder' => '----------------------------',
                                'data' => $options['data']->getDiplomeId()))
                                
            ->add('departementId' , EntityType::class, array(
                                    'class' => Departement::class,
                                    'label'=>'departement',
                                    'choice_label' => 'libelleDep',
                                    'mapped' => true,
                                    'placeholder' => '----------------------------',
                                    'data' => $options['data']->getDepartementId()))

            ->add('specialiteId' , EntityType::class, array(
                                        'class' => Specialite::class,
                                        'label'=>'spec',
                                        'choice_label' => 'libelleSpecialite',
                                        'mapped' => true,
                                        'placeholder' => '----------------------------',
                                        'data' => $options['data']->getSpecialiteId()))

            ->add('typePersonnelId' , EntityType::class, array(
                                            'class' => TypePersonnel::class,
                                            'label'=>'type_perso',
                                            'choice_label' => 'libellePersonnel',
                                            'mapped' => true,
                                            'placeholder' => '----------------------------',
                                            'data' => $options['data']->getTypePersonnelId()))


            ->add('activite', ChoiceType::class, [
                'label'=>'activite',
                                                'choices'  => [
                                                    'Normale' => 'N',
                                                    'Retraite' => 'R',
                                                    'Mutation' => "M",
                                                    'Abondon' =>  "A",
                                                    'Décès' =>  "D",
                                                ],
                                            ])

            ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        
         // Search for selected City and convert it into an Entity
        $corps = $this->em->getRepository(Corps::class)->find($data['corpsId']);
        // Search for selected City and convert it into an Entity
        $grade = $this->em->getRepository(Grades::class)->find($data['gradeId']);

        $this->addElements($form, $corps, $grade);
    }

    function onPreSetData(FormEvent $event) {
        $personnel = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
        $corps = $personnel->getCorpsId() ? $personnel->getCorpsId() : null;
        $grade = $personnel->getGradeId() ? $personnel->getGradeId() : null;

        $this->addElements($form, $corps, $grade);
    }






    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
        ]);
    }




    protected function addElements(FormInterface $form, Corps $corps = null , Grades $grades = null) {
        // 4. Add the province element
        $form->add('corpsId', EntityType::class, array(
            'label'=>'corps',
            'class' => Corps::class,
            'required' => true,
            'choice_label' => 'designationFR',
            'data' => $corps,
            'placeholder' => '----------------------------',
            
        ));
        
        // Neighborhoods empty, unless there is a selected City (Edit View)
        $grade = array();
        $echelon = array();
        // If there is a city stored in the Person entity, load the neighborhoods of it
        if ($corps) {
            // Fetch Neighborhoods of the City if there's a selected city

            $grade = $this->em->getRepository(Grades::class);
            
            $grade = $grade->createQueryBuilder("g")
                ->where("g.corpsId = :corpsid")
                ->setParameter("corpsid", $corps->getId())
                ->getQuery()
                ->getResult();
        }
         // If there is a city stored in the Person entity, load the neighborhoods of it
        if ($grades) {
            // Fetch Neighborhoods of the City if there's a selected city

            $echelon = $this->em->getRepository(Echelon::class);
                
            $echelon = $echelon->createQueryBuilder("ech")
                ->where("ech.grade = :gradeid")
                ->setParameter("gradeid", $grades->getId())
                ->getQuery()
                ->getResult(); 
                    

              
        }
        // Add the Neighborhoods field with the properly data
        $form->add('echelonId', EntityType::class, array(
            'label'=>'echelon',
            'required' => true,
            'placeholder' => '----------------------------',
            'class' => Echelon::class,
            'choice_label' => 'designation',
            'choices' => $echelon
        ));

        // Add the Neighborhoods field with the properly data
        $form->add('gradeId', EntityType::class, array(
            'label'=>'grade',
            'required' => true,
            'placeholder' => '----------------------------',
            'class' => Grades::class,
            'choice_label' => 'designationFR',
            'choices' => $grade
            ));


       
     
    }














}
