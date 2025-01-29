<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240429201016 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE TABLE av_flow (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, run_automatically BOOLEAN DEFAULT NULL, last_run_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, run_pattern VARCHAR(50) DEFAULT NULL, parameters JSONB DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE TABLE av_flow_step (id VARCHAR(36) NOT NULL, flow_id VARCHAR(36) NOT NULL, name VARCHAR(80) NOT NULL, description VARCHAR(200) DEFAULT NULL, action VARCHAR(80) NOT NULL, "order" INT NOT NULL, inputs JSONB DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_8DF096E87EB60D1B ON av_flow_step (flow_id)');
		$this->addSql('ALTER TABLE av_flow_step ADD CONSTRAINT FK_8DF096E87EB60D1B FOREIGN KEY (flow_id) REFERENCES av_flow (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_flow_step DROP CONSTRAINT FK_8DF096E87EB60D1B');
		$this->addSql('DROP TABLE av_flow');
		$this->addSql('DROP TABLE av_flow_step');
		$this->addSql('ALTER TABLE provider_person ADD number_of_activities INT DEFAULT 0 NOT NULL');
	}
}
