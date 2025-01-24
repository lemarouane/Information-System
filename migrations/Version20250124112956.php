<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add creator and personnel_full_name fields to the publication table.
 */
final class Version20250124112956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add creator and personnel_full_name columns to the publication table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE publication ADD creator VARCHAR(255) DEFAULT NULL, ADD personnel_full_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE publication DROP creator, DROP personnel_full_name');
    }
}
