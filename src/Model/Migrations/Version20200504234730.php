<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504234730 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE qbo_provider (qbo_provider_id VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, given_name VARCHAR(255) DEFAULT NULL, display_name VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, lat VARCHAR(255) DEFAULT NULL, long VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, family_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, acct_num VARCHAR(255) DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, uri VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT NULL, balance NUMERIC(19, 2) DEFAULT NULL, metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(qbo_provider_id))');
		$this->addSql('CREATE INDEX IDX_B66359A237F9A77 ON qbo_provider (qbo_provider_id)');

		$this->addSql('DROP INDEX idx_6ebfce57bf396750');
		$this->addSql('ALTER TABLE qbo_account RENAME COLUMN id TO qbo_account_id');
		$this->addSql('CREATE INDEX IDX_6EBFCE57115A9B6 ON qbo_account (qbo_account_id)');
		$this->addSql('DROP INDEX idx_826f5a05bf396750');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD customer_invoice_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD type VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD line_netto NUMERIC(19, 2) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD qbo_item_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD quantity NUMERIC(19, 2) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD qbo_account_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP detail_type');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP amount');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP qty');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP item_account_ref');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP item_ref_name');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP item_ref_value');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP metadata_create_time');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP metadata_last_updated_time');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item RENAME COLUMN id TO qbo_customer_invoice_item_id');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD CONSTRAINT FK_826F5A05D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_826F5A05D440F57F ON qbo_customer_invoice_item (customer_invoice_id)');
		$this->addSql('CREATE INDEX IDX_826F5A05FDD3CE4E ON qbo_customer_invoice_item (qbo_customer_invoice_item_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE qbo_provider');
		$this->addSql('DROP INDEX IDX_6EBFCE57115A9B6');
		$this->addSql('DROP INDEX qbo_account_pkey');
		$this->addSql('ALTER TABLE qbo_account RENAME COLUMN qbo_account_id TO id');
		$this->addSql('CREATE INDEX idx_6ebfce57bf396750 ON qbo_account (id)');
		$this->addSql('ALTER TABLE qbo_account ADD PRIMARY KEY (id)');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP CONSTRAINT FK_826F5A05D440F57F');
		$this->addSql('DROP INDEX IDX_826F5A05D440F57F');
		$this->addSql('DROP INDEX IDX_826F5A05FDD3CE4E');
		$this->addSql('DROP INDEX qbo_customer_invoice_item_pkey');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD detail_type VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD amount NUMERIC(19, 2) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD qty NUMERIC(19, 2) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD item_account_ref VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD item_ref_name VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD item_ref_value VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP customer_invoice_id');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP type');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP line_netto');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP qbo_item_id');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP quantity');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP qbo_account_id');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item RENAME COLUMN qbo_customer_invoice_item_id TO id');
		$this->addSql('CREATE INDEX idx_826f5a05bf396750 ON qbo_customer_invoice_item (id)');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD PRIMARY KEY (id)');
	}
}
