<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211209232246 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE cp_setting_report_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cp_setting_report (cp_setting_report_id BIGINT NOT NULL, predefined_data JSON DEFAULT NULL, PRIMARY KEY(cp_setting_report_id))');
		$this->addSql('CREATE INDEX IDX_C38F9EFF4A1CC83B ON cp_setting_report (cp_setting_report_id)');
		$this->addSql('ALTER TABLE cp_setting ADD cp_setting_report_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE cp_setting ALTER cp_setting_project_id DROP NOT NULL');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_A7E7925C4A1CC83B FOREIGN KEY (cp_setting_report_id) REFERENCES cp_setting_report (cp_setting_report_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A7E7925C4A1CC83B ON cp_setting (cp_setting_report_id)');
		$this->addSql('UPDATE cp_setting_project SET deadline_options_values = null');
		$this->addSql('ALTER TABLE cp_setting_project ALTER deadline_options_values DROP DEFAULT');
		$this->addSql('ALTER TABLE cp_setting_project ALTER deadline_options_values TYPE  JSON USING deadline_options_values::json');
		$this->addSql('ALTER TABLE cp_setting_project ALTER deadline_options_values TYPE JSON USING deadline_options_values::json');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT FK_A7E7925C4A1CC83B');
		$this->addSql('DROP SEQUENCE cp_setting_report_id_sequence CASCADE');
		$this->addSql('DROP TABLE cp_setting_report');
		$this->addSql('DROP INDEX UNIQ_A7E7925C4A1CC83B');
		$this->addSql('ALTER TABLE cp_setting DROP cp_setting_report_id');
		$this->addSql('ALTER TABLE cp_setting ALTER cp_setting_project_id SET NOT NULL');
		$this->addSql('ALTER TABLE cp_setting_project ALTER deadline_options_values TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE cp_setting_project ALTER deadline_options_values DROP DEFAULT');
	}
}
