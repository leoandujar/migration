<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200520214201 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE cp_setting_project_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE cp_setting_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cp_setting_project (cp_setting_project_id BIGINT NOT NULL, cp_setting_id BIGINT NOT NULL, working_files_as_ref_files BOOLEAN DEFAULT \'true\' NOT NULL, update_working_files BOOLEAN DEFAULT \'true\' NOT NULL, confirmation_send_by_default BOOLEAN DEFAULT \'false\' NOT NULL, download_confirmation BOOLEAN DEFAULT \'true\' NOT NULL, deadline_options VARCHAR(255) NOT NULL, deadline_options_values VARCHAR(255) DEFAULT NULL, specialization_required BOOLEAN DEFAULT \'true\' NOT NULL, send_source_files BOOLEAN DEFAULT \'false\' NOT NULL, custom_fields JSON DEFAULT NULL, PRIMARY KEY(cp_setting_project_id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E863D8B37EFE8D00 ON cp_setting_project (cp_setting_id)');
		$this->addSql('CREATE INDEX IDX_E863D8B33C03FA64 ON cp_setting_project (cp_setting_project_id)');
		$this->addSql('CREATE TABLE cp_setting (cp_setting_id BIGINT NOT NULL, customer_id BIGINT NOT NULL, PRIMARY KEY(cp_setting_id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A7E7925C81398E09 ON cp_setting (customer_id)');
		$this->addSql('CREATE INDEX IDX_A7E7925C7EFE8D00 ON cp_setting (cp_setting_id)');
		$this->addSql('ALTER TABLE cp_setting_project ADD CONSTRAINT FK_E863D8B37EFE8D00 FOREIGN KEY (cp_setting_id) REFERENCES cp_setting (cp_setting_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_A7E7925C81398E09 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting_project DROP CONSTRAINT FK_E863D8B37EFE8D00');
		$this->addSql('DROP SEQUENCE cp_setting_project_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE cp_setting_id_sequence CASCADE');
		$this->addSql('DROP TABLE cp_setting_project');
		$this->addSql('DROP TABLE cp_setting');
	}
}
