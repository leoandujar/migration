<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240123065156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add customer rule table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE av_customer_rule (av_customer_rule_id VARCHAR(36) NOT NULL, customer_id BIGINT DEFAULT NULL, workflow_id BIGINT DEFAULT NULL, name VARCHAR(50) NOT NULL, event VARCHAR(50) NOT NULL, type VARCHAR(30) NOT NULL, filters JSONB DEFAULT NULL, parameters JSONB DEFAULT NULL, PRIMARY KEY(av_customer_rule_id))');
        $this->addSql('CREATE INDEX IDX_3396684F9395C3F3 ON av_customer_rule (customer_id)');
        $this->addSql('CREATE INDEX IDX_3396684F2C7C2CBA ON av_customer_rule (workflow_id)');
        $this->addSql('ALTER TABLE av_customer_rule ADD CONSTRAINT FK_3396684F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE av_customer_rule ADD CONSTRAINT FK_3396684F2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES av_workflow (wf_workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE av_customer_rule DROP CONSTRAINT FK_3396684F9395C3F3');
        $this->addSql('ALTER TABLE av_customer_rule DROP CONSTRAINT FK_3396684F2C7C2CBA');
        $this->addSql('DROP TABLE av_customer_rule');
    }
}
