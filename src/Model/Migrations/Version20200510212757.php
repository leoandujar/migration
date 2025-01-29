<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200510212757 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE qbo_payment_item (qbo_payment_item_id VARCHAR(255) NOT NULL, qbo_customer_payment_id VARCHAR(255) DEFAULT NULL, xtrf_customer_invoice_id BIGINT DEFAULT NULL, qbo_provider_payment_id VARCHAR(255) DEFAULT NULL, qbo_provider_invoice_id VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, line_netto NUMERIC(19, 2) DEFAULT NULL, line_num INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, discount_rate NUMERIC(19, 2) DEFAULT NULL, discount_amt NUMERIC(19, 2) DEFAULT NULL, qbo_item_id VARCHAR(255) DEFAULT NULL, unit_price NUMERIC(19, 2) DEFAULT NULL, quantity NUMERIC(19, 2) DEFAULT NULL, qbo_account_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(qbo_payment_item_id))');
		$this->addSql('CREATE INDEX IDX_C6F330312690D7B3 ON qbo_payment_item (qbo_customer_payment_id)');
		$this->addSql('CREATE INDEX IDX_C6F330312727A733 ON qbo_payment_item (xtrf_customer_invoice_id)');
		$this->addSql('CREATE INDEX IDX_C6F33031FEEF9F89 ON qbo_payment_item (qbo_provider_payment_id)');
		$this->addSql('CREATE INDEX IDX_C6F33031D3A5CDCF ON qbo_payment_item (qbo_provider_invoice_id)');
		$this->addSql('CREATE INDEX IDX_C6F33031B81ADF04 ON qbo_payment_item (qbo_payment_item_id)');
		$this->addSql('ALTER TABLE qbo_payment_item ADD CONSTRAINT FK_C6F330312690D7B3 FOREIGN KEY (qbo_customer_payment_id) REFERENCES qbo_customer_payment (qbo_customer_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_payment_item ADD CONSTRAINT FK_C6F330312727A733 FOREIGN KEY (xtrf_customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_payment_item ADD CONSTRAINT FK_C6F33031FEEF9F89 FOREIGN KEY (qbo_provider_payment_id) REFERENCES qbo_provider_payment (qbo_provider_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_payment_item ADD CONSTRAINT FK_C6F33031D3A5CDCF FOREIGN KEY (qbo_provider_invoice_id) REFERENCES qbo_provider_invoice (qbo_provider_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE qbo_customer_payment DROP CONSTRAINT fk_ba27afbc2727a733');
		$this->addSql('DROP INDEX idx_ba27afbc2727a733');
		$this->addSql('ALTER TABLE qbo_customer_payment ADD linked_transaction_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_payment ADD linked_transaction_type VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_payment DROP xtrf_customer_invoice_id');
		$this->addSql('ALTER TABLE qbo_provider_payment DROP CONSTRAINT fk_56a2a9a5d3a5cdcf');
		$this->addSql('DROP INDEX idx_56a2a9a5d3a5cdcf');
		$this->addSql('ALTER TABLE qbo_provider_payment DROP qbo_provider_invoice_id');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE qbo_payment_item');
		$this->addSql('ALTER TABLE qbo_customer_payment ADD xtrf_customer_invoice_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_payment DROP linked_transaction_id');
		$this->addSql('ALTER TABLE qbo_customer_payment DROP linked_transaction_type');
		$this->addSql('ALTER TABLE qbo_customer_payment ADD CONSTRAINT fk_ba27afbc2727a733 FOREIGN KEY (xtrf_customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_ba27afbc2727a733 ON qbo_customer_payment (xtrf_customer_invoice_id)');
		$this->addSql('ALTER TABLE qbo_provider_payment ADD qbo_provider_invoice_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_provider_payment ADD CONSTRAINT fk_56a2a9a5d3a5cdcf FOREIGN KEY (qbo_provider_invoice_id) REFERENCES qbo_provider_invoice (qbo_provider_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_56a2a9a5d3a5cdcf ON qbo_provider_payment (qbo_provider_invoice_id)');
	}
}
