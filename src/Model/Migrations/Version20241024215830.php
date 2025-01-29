<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024215830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Implements the av_flow_monitor table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE av_flow_monitor (av_flow_monitor_id VARCHAR(255) NOT NULL, status INT NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, details JSON DEFAULT NULL, result JSON DEFAULT NULL, auxiliary_data JSON DEFAULT NULL, created_by BIGINT DEFAULT NULL, av_flow VARCHAR(36) NOT NULL, PRIMARY KEY(av_flow_monitor_id))');
        $this->addSql('CREATE INDEX IDX_C9B11C3CDE12AB56 ON av_flow_monitor (created_by)');
        $this->addSql('CREATE INDEX IDX_C9B11C3CA4112785 ON av_flow_monitor (av_flow)');
        $this->addSql('CREATE INDEX IDX_C9B11C3C1121074A ON av_flow_monitor (av_flow_monitor_id)');
        $this->addSql('ALTER TABLE av_flow_monitor ADD CONSTRAINT FK_C9B11C3CDE12AB56 FOREIGN KEY (created_by) REFERENCES av_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE av_flow_monitor ADD CONSTRAINT FK_C9B11C3CA4112785 FOREIGN KEY (av_flow) REFERENCES av_flow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE av_flow_monitor DROP CONSTRAINT FK_C9B11C3CDE12AB56');
        $this->addSql('ALTER TABLE av_flow_monitor DROP CONSTRAINT FK_C9B11C3CA4112785');
        $this->addSql('DROP TABLE av_flow_monitor');
    }
}
