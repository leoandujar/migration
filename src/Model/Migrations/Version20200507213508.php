<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200507213508 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE qbo_item (qbo_item_id VARCHAR(255) NOT NULL, fully_qualified_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(qbo_item_id))');
		$this->addSql('CREATE INDEX IDX_644F0C3C1E6CAAD ON qbo_item (qbo_item_id)');

		$this->addSql('CREATE TABLE qbo_customer_payment (qbo_customer_payment_id VARCHAR(255) NOT NULL, xtrf_customer_payment_id BIGINT DEFAULT NULL, xtrf_customer_invoice_id BIGINT DEFAULT NULL, customer_id VARCHAR(255) DEFAULT NULL, qbo_account_id VARCHAR(255) DEFAULT NULL, payment_method_id VARCHAR(255) DEFAULT NULL, payment_ref_num VARCHAR(255) DEFAULT NULL, total_amount NUMERIC(19, 2) DEFAULT NULL, unapplied_amount NUMERIC(19, 2) DEFAULT NULL, process_payment VARCHAR(255) DEFAULT NULL, transaction_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, exchange_rate VARCHAR(255) DEFAULT NULL, private_note VARCHAR(255) DEFAULT NULL, transaction_type VARCHAR(255) DEFAULT NULL, transaction_id VARCHAR(255) DEFAULT NULL, metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(qbo_customer_payment_id))');
		$this->addSql('CREATE INDEX IDX_BA27AFBCA6DF575 ON qbo_customer_payment (xtrf_customer_payment_id)');
		$this->addSql('CREATE INDEX IDX_BA27AFBC2727A733 ON qbo_customer_payment (xtrf_customer_invoice_id)');
		$this->addSql('CREATE INDEX IDX_BA27AFBC2690D7B3 ON qbo_customer_payment (qbo_customer_payment_id)');

		$this->addSql('CREATE TABLE qbo_provider_payment (qbo_provider_payment_id VARCHAR(255) NOT NULL, xtrf_provider_payment_id BIGINT DEFAULT NULL, qbo_provider_invoice_id VARCHAR(255) DEFAULT NULL, provider_id VARCHAR(255) DEFAULT NULL, pay_type VARCHAR(255) DEFAULT NULL, doc_number VARCHAR(255) DEFAULT NULL, qbo_account_id VARCHAR(255) DEFAULT NULL, total_amount NUMERIC(19, 2) DEFAULT NULL, transaction_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(qbo_provider_payment_id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_56A2A9A5D212BD4F ON qbo_provider_payment (xtrf_provider_payment_id)');
		$this->addSql('CREATE INDEX IDX_56A2A9A5D3A5CDCF ON qbo_provider_payment (qbo_provider_invoice_id)');
		$this->addSql('CREATE INDEX IDX_56A2A9A5FEEF9F89 ON qbo_provider_payment (qbo_provider_payment_id)');

		$this->addSql('ALTER TABLE qbo_customer_payment ADD CONSTRAINT FK_BA27AFBCA6DF575 FOREIGN KEY (xtrf_customer_payment_id) REFERENCES customer_payment (customer_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_customer_payment ADD CONSTRAINT FK_BA27AFBC2727A733 FOREIGN KEY (xtrf_customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE qbo_provider_payment ADD CONSTRAINT FK_56A2A9A5D212BD4F FOREIGN KEY (xtrf_provider_payment_id) REFERENCES provider_payment (provider_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_provider_payment ADD CONSTRAINT FK_56A2A9A5D3A5CDCF FOREIGN KEY (qbo_provider_invoice_id) REFERENCES qbo_provider_invoice (qbo_provider_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE qbo_provider_invoice ALTER currency_id DROP NOT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('DROP TABLE qbo_item');
		$this->addSql('DROP TABLE qbo_customer_payment');
		$this->addSql('DROP TABLE qbo_provider_payment');
		$this->addSql('ALTER TABLE qbo_provider_invoice ALTER currency_id SET NOT NULL');
	}
}
