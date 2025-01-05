<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\DBAL\Connection;
use App\Entity\Etudiant\Absence;
use Symfony\Component\OptionsResolver\OptionsResolver;

class absenceEtudiantType extends AbstractType
{

    private $conn;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
 

        $choices = [];
        $etapeChoices = $this->conn->fetchAllAssociative("select distinct(ins_adm_etp.COD_ETP)
                                                    from ins_adm_etp
                                                    where ins_adm_etp.COD_ANU='2021'
                                                        AND ins_adm_etp.ETA_IAE='E'
                                                        AND ins_adm_etp.COD_CMP='ENT'
                                                    order by ins_adm_etp.COD_ETP ASC");
        foreach ($etapeChoices as $choice) {
          $choices[$choice['COD_ETP']] = $choice['COD_ETP'];
        }
        $builder
            ->add('dateabsence',DateType::class, array('widget' => 'single_text',
                                                    'required'=>true,
                                                    'html5' => false))
            ->add('seance',ChoiceType::class, array(
                'choices' => array('séance 1 (9h00 - 10h30)' => '9h00 - 10h30', 'séance 2 (10h45 - 12h15)' => '10h45 - 12h15','séance 3 (13h30 - 15h00)' => '13h30 - 15h00','séance 4 (15h15 - 16h45)' => '15h15 - 16h45'),
                'required' => true,
                'placeholder' => '--------------',
            ))
           ->add('etape', ChoiceType::class, [
                'choices' => $choices,
                'required' => true,
                'placeholder' => '---------------',

                ]);
       





            // 3. Add 2 event listeners for the form
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
     }

   protected function addElements(FormInterface $form, $etape = null)
    {
        if($etape){
            $choices = [];
            $moduleChoices = $this->conn->fetchAllAssociative("select distinct(ic.COD_ELP) from ind_contrat_elp ic
                                                        left outer join element_pedagogi ep 
                                                            on ic.COD_ELP=ep.COD_ELP
                                                    where ic.COD_ETP='".$etape."' 
                                                        and ep.COD_NEL='MO' 
                                                    order by ic.COD_ELP asc ");
            foreach ($moduleChoices as $choice) {
              $choices[$choice['COD_ELP']] = $choice['COD_ELP'];
            }
            $form->add('module', ChoiceType::class, [
                'choices' => $choices,
                'required' => true,
                'placeholder' => '---------------',

                ]);
        }
        else{
            $form->add('module',ChoiceType::class,array(
                'required' => true,
                'placeholder' => '---------------',
                'choices' => array())
            );
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addElements($form, $data['etape']);
    }

    public function onPreSetData(FormEvent $event)
    {
        $absence = $event->getData();
        $form = $event->getForm();

        $this->addElements($form, $absence->getEtape());
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }

    public function getName()
    {
        return 'absenceetudianttype';
    }
}