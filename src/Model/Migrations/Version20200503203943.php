<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503203943 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE qbo_customer_invoice_item (id VARCHAR(255) NOT NULL, detail_type VARCHAR(255) DEFAULT NULL, amount NUMERIC(19, 2) DEFAULT NULL, line_num INT DEFAULT NULL, qty NUMERIC(19, 2) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, item_account_ref VARCHAR(255) DEFAULT NULL, discount_rate NUMERIC(19, 2) DEFAULT NULL, discount_amt NUMERIC(19, 2) DEFAULT NULL, unit_price NUMERIC(19, 2) DEFAULT NULL, item_ref_name VARCHAR(255) DEFAULT NULL, item_ref_value VARCHAR(255) DEFAULT NULL, metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_826F5A05BF396750 ON qbo_customer_invoice_item (id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE qbo_customer_invoice_item');
	}
}
