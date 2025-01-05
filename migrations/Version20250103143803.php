<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250103143803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create doctorants table';
    }

    public function up(Schema $schema): void
    {
        // This migration creates the doctorants table in the pgi_doc_db database
        $this->addSql('CREATE TABLE pgi_doc_db.doctorants (
            id INT AUTO_INCREMENT NOT NULL, 
            email VARCHAR(255) NOT NULL, 
            code VARCHAR(255) NOT NULL, 
            nom VARCHAR(255) NOT NULL, 
            prenom VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // This migration removes the doctorants table
        $this->addSql('DROP TABLE pgi_doc_db.doctorants');
    }
}
