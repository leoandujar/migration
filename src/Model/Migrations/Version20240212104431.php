<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212104431 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add xtrf_currency_history';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('CREATE SEQUENCE xtrf_currency_history_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE xtrf_currency_history (xtrf_currency_history_id BIGINT NOT NULL, xtrf_currency_id BIGINT NOT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version INT NOT NULL, exchange_rate NUMERIC(19, 5) NOT NULL, from_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, origin_details VARCHAR(255) NOT NULL, publication_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(xtrf_currency_history_id))');
		$this->addSql('CREATE INDEX IDX_B2E4DBE674B55F40 ON xtrf_currency_history (xtrf_currency_id)');
		$this->addSql('ALTER TABLE xtrf_currency_history ADD CONSTRAINT FK_B2E4DBE674B55F40 FOREIGN KEY (xtrf_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('DROP SEQUENCE xtrf_currency_history_id_sequence CASCADE');
		$this->addSql('ALTER TABLE xtrf_currency_history DROP CONSTRAINT FK_B2E4DBE674B55F40');
		$this->addSql('DROP TABLE xtrf_currency_history');
	}
}
