<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200523224017 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting_project DROP CONSTRAINT fk_e863d8b37efe8d00');
		$this->addSql('DROP INDEX uniq_e863d8b37efe8d00');
		$this->addSql('ALTER TABLE cp_setting_project DROP cp_setting_id');
		$this->addSql('ALTER TABLE cp_setting ADD cp_setting_project_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_A7E7925C3C03FA64 FOREIGN KEY (cp_setting_project_id) REFERENCES cp_setting_project (cp_setting_project_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A7E7925C3C03FA64 ON cp_setting (cp_setting_project_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT FK_A7E7925C3C03FA64');
		$this->addSql('DROP INDEX UNIQ_A7E7925C3C03FA64');
		$this->addSql('ALTER TABLE cp_setting DROP cp_setting_project_id');
		$this->addSql('ALTER TABLE cp_setting_project ADD cp_setting_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE cp_setting_project ADD CONSTRAINT fk_e863d8b37efe8d00 FOREIGN KEY (cp_setting_id) REFERENCES cp_setting (cp_setting_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX uniq_e863d8b37efe8d00 ON cp_setting_project (cp_setting_id)');
	}
}
