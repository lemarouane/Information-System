<?php

namespace App\Form;

use App\Entity\Doctorants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctorantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Etablissement')
            ->add('Nom')
            ->add('prenom')
            ->add('nomArabe')
            ->add('prenomArabe')
            ->add('cin')
            ->add('cne')
            ->add('dateDeNaissance')
            ->add('lieuDeNaissance')
            ->add('provinceDeNaissance')
            ->add('paysNaissance')
            ->add('nationalite')
            ->add('sexe')
            ->add('telephone')
            ->add('email')
            ->add('emailInstitutionnel')
            ->add('codePostal')
            ->add('organismeEmployeur')
            ->add('profession')
            ->add('academieBac')
            ->add('provinceBac')
            ->add('lyceeBac')
            ->add('mentionBac')
            ->add('noteBac')
            ->add('dateObtentionBac')
            ->add('paysBac')
            ->add('secteurBac')
            ->add('specialiteBac')
            ->add('licenceEtablissement')
            ->add('mentionLicence')
            ->add('dateObtentionLicence')
            ->add('licencePays')
            ->add('licenceSpecialite')
            ->add('universiteLicence')
            ->add('noteLicence')
            ->add('licenceSecteur')
            ->add('masterEtablissement')
            ->add('noteMaster')
            ->add('mentionMaster')
            ->add('dateObtentionMaster')
            ->add('paysObtentionMaster')
            ->add('masterSpecialite')
            ->add('masterUniversite')
            ->add('masterSecteur')
            ->add('autreDiplomeType')
            ->add('autreDiplomeEtablissement')
            ->add('autreDiplomeMention')
            ->add('autreDiplomeDateObtention')
            ->add('autreDiplomePays')
            ->add('autreDiplomeSpecialite')
            ->add('autreDiplomeUniversite')
            ->add('autreDiplomeSecteur')
            ->add('langue1')
            ->add('langue2')
            ->add('langue3')
            ->add('langue4')
            ->add('niveauLangue1')
            ->add('niveauLangue2')
            ->add('niveauLangue3')
            ->add('niveauLangue4')
            ->add('etablissement1')
            ->add('etablissement2')
            ->add('etablissement3')
            ->add('experience1')
            ->add('experience2')
            ->add('experience3')
            ->add('fonction1')
            ->add('fonction2')
            ->add('fonction3')
            ->add('periode1D')
            ->add('periode1F')
            ->add('periode2D')
            ->add('periode2F')
            ->add('periode3D')
            ->add('periode3F')
            ->add('secteur1')
            ->add('secteur2')
            ->add('secteur3')
            ->add('handicape')
            ->add('ced')
            ->add('fd')
            ->add('enseignantChercheur')
            ->add('choix')
            ->add('sujet')
            ->add('n_etabliss')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Doctorants::class,
        ]);
    }
}
