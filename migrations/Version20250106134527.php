<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250106134527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the doctorants table based on the Excel field order and drops redundant tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE doctorants (
                id INT AUTO_INCREMENT NOT NULL,
                etablissement VARCHAR(255) NOT NULL,
                nom VARCHAR(255) NOT NULL,
                prenom VARCHAR(255) NOT NULL,
                nom_arabe VARCHAR(255) DEFAULT NULL,
                prenom_arabe VARCHAR(255) DEFAULT NULL,
                cin VARCHAR(255) NOT NULL,
                cne VARCHAR(255) NOT NULL,
                date_de_naissance DATETIME DEFAULT NULL,
                lieu_de_naissance VARCHAR(255) DEFAULT NULL,
                province_de_naissance VARCHAR(255) DEFAULT NULL,
                pays_naissance VARCHAR(255) DEFAULT NULL,
                nationalite VARCHAR(255) DEFAULT NULL,
                sexe VARCHAR(255) DEFAULT NULL,
                telephone VARCHAR(255) DEFAULT NULL,
                email VARCHAR(255) DEFAULT NULL,
                email_institutionnel VARCHAR(255) DEFAULT NULL,
                code_postal VARCHAR(255) DEFAULT NULL,
                organisme_employeur VARCHAR(255) DEFAULT NULL,
                profession VARCHAR(255) DEFAULT NULL,
                academie_bac VARCHAR(255) DEFAULT NULL,
                province_bac VARCHAR(255) DEFAULT NULL,
                lycee_bac VARCHAR(255) DEFAULT NULL,
                mention_bac VARCHAR(255) DEFAULT NULL,
                note_bac DOUBLE DEFAULT NULL,
                date_obtention_bac VARCHAR(255) DEFAULT NULL,
                pays_bac VARCHAR(255) DEFAULT NULL,
                secteur_bac VARCHAR(255) DEFAULT NULL,
                specialite_bac VARCHAR(255) DEFAULT NULL,
                licence_etablissement VARCHAR(255) DEFAULT NULL,
                mention_licence VARCHAR(255) DEFAULT NULL,
                date_obtention_licence VARCHAR(255) DEFAULT NULL,
                licence_pays VARCHAR(255) DEFAULT NULL,
                licence_specialite VARCHAR(255) DEFAULT NULL,
                universite_licence VARCHAR(255) DEFAULT NULL,
                note_licence DOUBLE DEFAULT NULL,
                licence_secteur VARCHAR(255) DEFAULT NULL,
                master_etablissement VARCHAR(255) DEFAULT NULL,
                note_master DOUBLE DEFAULT NULL,
                mention_master VARCHAR(255) DEFAULT NULL,
                date_obtention_master VARCHAR(255) DEFAULT NULL,
                pays_obtention_master VARCHAR(255) DEFAULT NULL,
                master_specialite VARCHAR(255) DEFAULT NULL,
                master_universite VARCHAR(255) DEFAULT NULL,
                master_secteur VARCHAR(255) DEFAULT NULL,
                autre_diplome_type VARCHAR(255) DEFAULT NULL,
                autre_diplome_etablissement VARCHAR(255) DEFAULT NULL,
                autre_diplome_mention VARCHAR(255) DEFAULT NULL,
                autre_diplome_date_obtention VARCHAR(255) DEFAULT NULL,
                autre_diplome_pays VARCHAR(255) DEFAULT NULL,
                autre_diplome_specialite VARCHAR(255) DEFAULT NULL,
                autre_diplome_universite VARCHAR(255) DEFAULT NULL,
                autre_diplome_secteur VARCHAR(255) DEFAULT NULL,
                langue1 VARCHAR(255) DEFAULT NULL,
                langue2 VARCHAR(255) DEFAULT NULL,
                langue3 VARCHAR(255) DEFAULT NULL,
                langue4 VARCHAR(255) DEFAULT NULL,
                niveau_langue1 VARCHAR(255) DEFAULT NULL,
                niveau_langue2 VARCHAR(255) DEFAULT NULL,
                niveau_langue3 VARCHAR(255) DEFAULT NULL,
                niveau_langue4 VARCHAR(255) DEFAULT NULL,
                etablissement1 VARCHAR(255) DEFAULT NULL,
                etablissement2 VARCHAR(255) DEFAULT NULL,
                etablissement3 VARCHAR(255) DEFAULT NULL,
                experience1 VARCHAR(255) DEFAULT NULL,
                experience2 VARCHAR(255) DEFAULT NULL,
                experience3 VARCHAR(255) DEFAULT NULL,
                fonction1 VARCHAR(255) DEFAULT NULL,
                fonction2 VARCHAR(255) DEFAULT NULL,
                fonction3 VARCHAR(255) DEFAULT NULL,
                periode1_d VARCHAR(255) DEFAULT NULL,
                periode1_f VARCHAR(255) DEFAULT NULL,
                periode2_d VARCHAR(255) DEFAULT NULL,
                periode2_f VARCHAR(255) DEFAULT NULL,
                periode3_d VARCHAR(255) DEFAULT NULL,
                periode3_f VARCHAR(255) DEFAULT NULL,
                secteur1 VARCHAR(255) DEFAULT NULL,
                secteur2 VARCHAR(255) DEFAULT NULL,
                secteur3 VARCHAR(255) DEFAULT NULL,
                handicape VARCHAR(255) DEFAULT NULL,
                ced VARCHAR(255) DEFAULT NULL,
                fd VARCHAR(255) DEFAULT NULL,
                etablissement_1  VARCHAR(255) DEFAULT NULL,
                enseignant_chercheur VARCHAR(255) DEFAULT NULL,
                choix VARCHAR(255) DEFAULT NULL,
                sujet LONGTEXT DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        ');


    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS doctorants');
    }
}
