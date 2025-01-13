<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for adding structure_id to validated_doctorants.
 */
final class Version20250113095339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add structure_id column to validated_doctorants table and establish foreign key relationship with struct_rech table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE validated_doctorants ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE validated_doctorants ADD CONSTRAINT FK_2272AC712534008B FOREIGN KEY (structure_id) REFERENCES struct_rech (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2272AC712534008B ON validated_doctorants (structure_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE validated_doctorants DROP FOREIGN KEY FK_2272AC712534008B');
        $this->addSql('DROP INDEX IDX_2272AC712534008B ON validated_doctorants');
        $this->addSql('ALTER TABLE validated_doctorants DROP structure_id');
    }
}
