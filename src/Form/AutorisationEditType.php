<?php

namespace App\Form;

use App\Entity\Autorisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class AutorisationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('id', TextType::class, [ 'disabled' => true  , 'translation_domain' => 'messages', 'label'=>'n'])
            ->add('motifAutorisation' , TextareaType::class, [
                'label_attr' => [
                'class' => 'form-label',
                'label'=>'motif'
            ]])
            ->add('dateSortie',DateTimeType::class, array('widget' => 'single_text' ,  'required' => false , 'label_attr' => [
                'class' => 'form-label',
            ],'html5' => false, 'label'=>'date_debut'))
            ->add('dateRentree',DateTimeType::class, array('widget' => 'single_text' ,  'required' => false , 'label_attr' => [
                'class' => 'form-label', 'label'=>'date_fin'
            ],'html5' => false, ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Autorisation::class,
        ]);
    }
}
