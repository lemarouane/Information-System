<?php
 
namespace App\Form;

use App\Entity\Attestation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AttestationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('dateEnvoie',DateType::class, array('widget' => 'single_text'))
            ->add('id', TextType::class, [ 'disabled' => true  , 'translation_domain' => 'messages', ])
            ->add('dateDebut',DateType::class, array('widget' => 'single_text' , 'required' => false , 'label'=>'date_debut'))
            ->add('dateFin',DateType::class, array('widget' => 'single_text' ,  'required' => false  , 'label'=>'date_fin'))
            ->add('type', ChoiceType::class, [
                'translation_domain' => 'messages',
                'choices'  => [
                    'att_salaire' => 'AS',
                    'att_travail' => 'AT',
                ],
                 'label'=>'type'
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attestation::class,
        ]);
    }
}
