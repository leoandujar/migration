<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200505232356 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE UNIQUE INDEX UNIQ_13E7151462B6A45E ON xtrf_currency (iso_code)');
		$this->addSql('CREATE TABLE qbo_provider_invoice (qbo_provider_invoice_id VARCHAR(255) NOT NULL, currency_id VARCHAR(255) NOT NULL, qbo_provider_id VARCHAR(255) DEFAULT NULL, xtrf_provider_id BIGINT DEFAULT NULL, qbo_account_id VARCHAR(255) DEFAULT NULL, final_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, total_netto NUMERIC(19, 2) DEFAULT NULL, required_payment_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, balance NUMERIC(19, 2) DEFAULT NULL, created_on_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(qbo_provider_invoice_id))');
		$this->addSql('CREATE INDEX IDX_ABEF3AEC37F9A77 ON qbo_provider_invoice (qbo_provider_id)');
		$this->addSql('CREATE INDEX IDX_ABEF3AEC46C2769C ON qbo_provider_invoice (xtrf_provider_id)');
		$this->addSql('CREATE INDEX IDX_ABEF3AECD3A5CDCF ON qbo_provider_invoice (qbo_provider_invoice_id)');
		$this->addSql('ALTER TABLE qbo_provider_invoice ADD CONSTRAINT FK_ABEF3AEC37F9A77 FOREIGN KEY (qbo_provider_id) REFERENCES qbo_provider (qbo_provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_provider_invoice ADD CONSTRAINT FK_ABEF3AEC46C2769C FOREIGN KEY (xtrf_provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE qbo_provider_invoice');
		$this->addSql('DROP INDEX UNIQ_13E7151462B6A45E');
	}
}
