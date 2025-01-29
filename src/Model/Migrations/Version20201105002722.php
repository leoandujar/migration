<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105002722 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE xtrf_user ADD COLUMN IF NOT EXISTS erased_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE contact_person ADD COLUMN IF NOT EXISTS erased_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE contact_person ADD COLUMN IF NOT EXISTS last_login_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE contact_person ADD COLUMN IF NOT EXISTS last_failed_login_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD COLUMN IF NOT EXISTS erased_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD COLUMN IF NOT EXISTS last_login_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD COLUMN IF NOT EXISTS last_failed_login_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD COLUMN IF NOT EXISTS chat_enabled boolean DEFAULT true NOT NULL');
		$this->addSql('ALTER TABLE quote ADD COLUMN IF NOT EXISTS chat_enabled boolean DEFAULT true NOT NULL');
		$this->addSql('ALTER TABLE provider_person ADD COLUMN IF NOT EXISTS invitation_sent boolean DEFAULT false NOT NULL');
		$this->addSql('ALTER TABLE tm_savings ADD COLUMN IF NOT EXISTS original_cat_tool VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD COLUMN IF NOT EXISTS erased_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE xtrf_user DROP erased_at');
		$this->addSql('ALTER TABLE contact_person DROP erased_at');
		$this->addSql('ALTER TABLE contact_person DROP last_login_date');
		$this->addSql('ALTER TABLE contact_person DROP last_failed_login_date');
		$this->addSql('ALTER TABLE customer DROP erased_at');
		$this->addSql('ALTER TABLE customer DROP last_login_date');
		$this->addSql('ALTER TABLE customer DROP last_failed_login_date');
		$this->addSql('ALTER TABLE project DROP chat_enabled');
		$this->addSql('ALTER TABLE quote DROP chat_enabled');
		$this->addSql('ALTER TABLE provider_person DROP invitation_sent');
		$this->addSql('ALTER TABLE tm_savings DROP original_cat_tool');
		$this->addSql('ALTER TABLE provider DROP erased_at');
	}
}
