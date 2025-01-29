<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419181410 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE hs_pipeline_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE hs_pipeline_stage_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE hs_pipeline (hs_pipeline_id BIGINT NOT NULL, hs_id VARCHAR(255) NOT NULL, label VARCHAR(70) DEFAULT NULL, display_order INT DEFAULT NULL, archived BOOLEAN DEFAULT \'false\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(hs_pipeline_id))');
		$this->addSql('CREATE INDEX IDX_29DB701D4C818790 ON hs_pipeline (hs_pipeline_id)');
		$this->addSql('CREATE TABLE hs_pipeline_stage (hs_pipeline_stage_id BIGINT NOT NULL, hs_pipeline_id BIGINT NOT NULL, hs_id VARCHAR(255) NOT NULL, label VARCHAR(70) DEFAULT NULL, display_order INT DEFAULT NULL, archived BOOLEAN DEFAULT \'false\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata JSON DEFAULT NULL, PRIMARY KEY(hs_pipeline_stage_id))');
		$this->addSql('CREATE INDEX IDX_C817CD884C818790 ON hs_pipeline_stage (hs_pipeline_id)');
		$this->addSql('CREATE INDEX IDX_C817CD8874E0A304 ON hs_pipeline_stage (hs_pipeline_stage_id)');
		$this->addSql('ALTER TABLE hs_pipeline_stage ADD CONSTRAINT FK_C817CD884C818790 FOREIGN KEY (hs_pipeline_id) REFERENCES hs_pipeline (hs_pipeline_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('ALTER TABLE hs_pipeline_stage DROP CONSTRAINT FK_C817CD884C818790');
		$this->addSql('DROP SEQUENCE hs_pipeline_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE hs_pipeline_stage_sequence CASCADE');
		$this->addSql('DROP TABLE hs_pipeline');
		$this->addSql('DROP TABLE hs_pipeline_stage');
	}
}
