<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200322234544 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE xtrf_language ADD language_code VARCHAR(2) DEFAULT NULL');
		$this->addSql('ALTER TABLE xtrf_language ADD country_code VARCHAR(2) DEFAULT NULL');
		$this->addSql('ALTER TABLE xtrf_language ADD script VARCHAR(4) DEFAULT NULL');
		$this->addSql('ALTER TABLE analytics_project ADD activity VARCHAR(50) DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD activity_name VARCHAR(255) DEFAULT \'\' NOT NULL');
		$this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_AC74095AA53A8AA ON activity (provider_id)');
		$this->addSql('CREATE SEQUENCE analytic_project_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE analytic_project_step_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE statistics_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE alert_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE analytics_project DROP activity');
		$this->addSql('ALTER TABLE xtrf_language DROP language_code');
		$this->addSql('ALTER TABLE xtrf_language DROP country_code');
		$this->addSql('ALTER TABLE xtrf_language DROP script');
		$this->addSql('ALTER TABLE activity DROP CONSTRAINT FK_AC74095AA53A8AA');
		$this->addSql('DROP INDEX IDX_AC74095AA53A8AA');
		$this->addSql('ALTER TABLE activity DROP activity_name');
		$this->addSql('ALTER TABLE activity ALTER provider_id DROP NOT NULL');
		$this->addSql('DROP SEQUENCE analytic_project_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE analytic_project_step_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE statistics_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE alert_id_sequence CASCADE');
	}
}
