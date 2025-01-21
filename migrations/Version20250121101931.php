<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250121101931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add scopus_id field to Personnel entity';
    }

    public function up(Schema $schema): void
    {
        // Add the scopus_id column to the personnel table
        $this->addSql('ALTER TABLE personnel ADD scopus_id VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the scopus_id column from the personnel table
        $this->addSql('ALTER TABLE personnel DROP scopus_id');
    }
}
