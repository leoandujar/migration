<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320150218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'to add data_new column to cp_template table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cp_template ADD data_new JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cp_template DROP data_new');
    }
}
