<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220113224554 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting_project DROP deadline_options');
		$this->addSql('ALTER TABLE cp_setting_project RENAME COLUMN deep_file_metrics TO deadline_prediction');
		$this->addSql('ALTER TABLE cp_setting_project RENAME COLUMN deadline_options_values TO deadline_options');
		$this->addSql('ALTER TABLE cp_setting_quote DROP deadline_options');
		$this->addSql('ALTER TABLE cp_setting_quote RENAME COLUMN deep_file_metrics TO deadline_prediction');
		$this->addSql('ALTER TABLE cp_setting_quote RENAME COLUMN deadline_options_values TO deadline_options');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting_project RENAME COLUMN deadline_options TO deadline_options_values');
		$this->addSql('ALTER TABLE cp_setting_project RENAME COLUMN deadline_prediction TO deep_file_metrics');
		$this->addSql('ALTER TABLE cp_setting_project ADD deadline_options VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE cp_setting_quote RENAME COLUMN deadline_options TO deadline_options_values');
		$this->addSql('ALTER TABLE cp_setting_quote RENAME COLUMN deadline_prediction TO deep_file_metrics');
		$this->addSql('ALTER TABLE cp_setting_quote ADD deadline_options VARCHAR(255) DEFAULT NULL');
	}
}
