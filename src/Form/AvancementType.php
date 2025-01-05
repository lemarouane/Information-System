<?php

namespace App\Form;

use App\Entity\Avancement;
use App\Entity\Personnel;
use App\Entity\Corps;
use App\Entity\Grades;
use App\Entity\Echelon;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;

class AvancementType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('derniere_situation', CheckboxType::class, [
            'label'    => 'derniere_situation',
            'required' => false,
            'mapped' => false,
            'label_attr' => array(
                'class' =>'form-label'
              ),
        ])

            ->add('arrete' ,TextType::class, ['mapped' => false])
            ->add('arreteFile', FileType::class, [
                'label' => 'Arrete',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
            
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
            
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2024k',
                        'mimeTypes' => [       
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG document',
                    ])
                ],
            ])


            ->add('numDeci', IntegerType::class, ['label' => 'num_deci'])
            ->add('dateDeci',DateType::class, array('widget' => 'single_text', 'label' => 'date_deci'))
            ->add('dateGrade',DateType::class, array('widget' => 'single_text' , 'label' => 'date_grade'))       

        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }


    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();
        
         // Search for selected City and convert it into an Entity
        $corps = $this->em->getRepository(Corps::class)->find($data['corps']);
        // Search for selected City and convert it into an Entity
        $grade = $this->em->getRepository(Grades::class)->find($data['grade']);

        $this->addElements($form, $corps, $grade);
    }

    function onPreSetData(FormEvent $event) {
        $avancement = $event->getData();
        $form = $event->getForm();

        // When you create a new person, the City is always empty
        $corps = $avancement->getCorps() ? $avancement->getCorps() : null;
        $grade = $avancement->getGrade() ? $avancement->getGrade() : null;

        $this->addElements($form, $corps, $grade);
    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avancement::class,
        ]);
    }



    protected function addElements(FormInterface $form, Corps $corps = null , Grades $grades = null) {
        // 4. Add the province element
        $form->add('corps', EntityType::class, array(
            'class' => Corps::class,
            'required' => true,
            'choice_label' => 'designationFR',
            'data' => $corps,
            'placeholder' => '------------'
            
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
        $form->add('echelon', EntityType::class, array(
            'required' => true,
            'placeholder' => '------------',
            'class' => Echelon::class,
            'choice_label' => 'designation',
            'choices' => $echelon
        ));

        // Add the Neighborhoods field with the properly data
        $form->add('grade', EntityType::class, array(
            'required' => true,
            'placeholder' => '------------',
            'class' => Grades::class,
            'choice_label' => 'designationFR',
            'choices' => $grade
            ));


       
     
    }







    
}
