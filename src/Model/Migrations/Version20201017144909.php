<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017144909 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE history_entry_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE history_entry (history_entry_id BIGINT NOT NULL, responsible_compound_id VARCHAR(255) DEFAULT NULL, responsible_name VARCHAR(255) DEFAULT NULL, timestamp INT NOT NULL, entities JSON DEFAULT NULL, actions JSON DEFAULT NULL, regions JSON DEFAULT NULL, initial BOOLEAN NOT NULL, PRIMARY KEY(history_entry_id))');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE history_entry_id_sequence CASCADE');
		$this->addSql('DROP TABLE history_entry');
	}
}
