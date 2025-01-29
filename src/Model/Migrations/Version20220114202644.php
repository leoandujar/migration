<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220114202644 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting_project ADD sendSource BOOLEAN DEFAULT \'true\' NOT NULL');
		$this->addSql('ALTER TABLE cp_setting_project RENAME COLUMN specialization_required TO rush');
		$this->addSql('ALTER TABLE cp_setting_quote ADD sendSource BOOLEAN DEFAULT \'true\' NOT NULL');
		$this->addSql('ALTER TABLE cp_setting_quote RENAME COLUMN specialization_required TO rush');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting_project ADD specialization_required BOOLEAN DEFAULT \'true\' NOT NULL');
		$this->addSql('ALTER TABLE cp_setting_project DROP rush');
		$this->addSql('ALTER TABLE cp_setting_project DROP sendSource');

		$this->addSql('ALTER TABLE cp_setting_quote ADD specialization_required BOOLEAN DEFAULT \'true\' NOT NULL');
		$this->addSql('ALTER TABLE cp_setting_quote DROP rush');
		$this->addSql('ALTER TABLE cp_setting_quote DROP sendSource');
	}
}
