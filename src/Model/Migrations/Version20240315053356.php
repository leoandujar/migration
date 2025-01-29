<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315053356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'drops refresh_tokens table and api_key columns from contact_person table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('ALTER TABLE contact_person DROP api_key');
        $this->addSql('ALTER TABLE contact_person DROP ap_api_key');
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE refresh_tokens (id INT DEFAULT NULL, refresh_token VARCHAR(128) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL)');
        $this->addSql('ALTER TABLE contact_person ADD api_key TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_person ADD ap_api_key TEXT DEFAULT NULL');
        
    }
}
