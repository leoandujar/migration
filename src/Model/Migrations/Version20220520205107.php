<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220520205107 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE xtm_edit_distance ALTER lower_score TYPE NUMERIC(19, 15)');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER lower_score DROP DEFAULT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER higher_score TYPE NUMERIC(19, 15)');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER higher_score DROP DEFAULT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER average_score TYPE NUMERIC(19, 15)');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER average_score DROP DEFAULT');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_B75BBF606D9546F ON xtm_edit_distance (analytics_project_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX UNIQ_B75BBF606D9546F');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER lower_score TYPE INT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER lower_score DROP DEFAULT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER higher_score TYPE INT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER higher_score DROP DEFAULT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER average_score TYPE INT');
		$this->addSql('ALTER TABLE xtm_edit_distance ALTER average_score DROP DEFAULT');
		$this->addSql('CREATE INDEX idx_b75bbf606d9546f ON xtm_edit_distance (analytics_project_id)');
	}
}
