<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250110144947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add personnel_id column to validated_doctorants table and set foreign key constraint.';
    }

    public function up(Schema $schema): void
    {
        // Add the column with NULL allowed initially
        $this->addSql('ALTER TABLE validated_doctorants ADD personnel_id INT DEFAULT NULL');

        // Populate existing rows with a valid personnel_id or leave them NULL
        // Uncomment and modify the following line if a default value is needed:
        // $this->addSql('UPDATE validated_doctorants SET personnel_id = (SELECT id FROM personnel LIMIT 1) WHERE personnel_id IS NULL');

        // Add the foreign key constraint
        $this->addSql('ALTER TABLE validated_doctorants ADD CONSTRAINT FK_2272AC711C109075 FOREIGN KEY (personnel_id) REFERENCES personnel (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2272AC711C109075 ON validated_doctorants (personnel_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE validated_doctorants DROP FOREIGN KEY FK_2272AC711C109075');
        $this->addSql('DROP INDEX IDX_2272AC711C109075 ON validated_doctorants');
        $this->addSql('ALTER TABLE validated_doctorants DROP personnel_id');
    }
}
