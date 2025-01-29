<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220519055345 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE xtm_edit_distance (id UUID NOT NULL, analytics_project_id BIGINT NOT NULL, segments_count INT NOT NULL, segments_zero_count INT NOT NULL, lower_score INT NOT NULL, higher_score INT NOT NULL, average_score INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_B75BBF606D9546F ON xtm_edit_distance (analytics_project_id)');
		$this->addSql('ALTER TABLE xtm_edit_distance ADD CONSTRAINT FK_B75BBF606D9546F FOREIGN KEY (analytics_project_id) REFERENCES analytics_project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE analytics_project ADD extended_table_file_id BIGINT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE xtm_edit_distance');
		$this->addSql('ALTER TABLE analytics_project DROP extended_table_file_id');
	}
}
