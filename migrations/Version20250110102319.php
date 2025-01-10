<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250110102319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a relationship between validated_doctorants and doctorants tables.';
    }

    public function up(Schema $schema): void
    {
        // Add the doctorant_id column and foreign key
        $this->addSql('ALTER TABLE validated_doctorants ADD doctorant_id INT NOT NULL');
        $this->addSql('ALTER TABLE validated_doctorants ADD CONSTRAINT FK_CF7C19DFEEF99363 FOREIGN KEY (doctorant_id) REFERENCES doctorants (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CF7C19DFEEF99363 ON validated_doctorants (doctorant_id)');
    }

    public function down(Schema $schema): void
    {
        // Rollback the changes
        $this->addSql('ALTER TABLE validated_doctorants DROP FOREIGN KEY FK_CF7C19DFEEF99363');
        $this->addSql('DROP INDEX IDX_CF7C19DFEEF99363 ON validated_doctorants');
        $this->addSql('ALTER TABLE validated_doctorants DROP doctorant_id');
    }
}
