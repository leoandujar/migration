<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220922004509 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE bl_provider_invoice_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE bl_provider_invoices (id BIGINT NOT NULL, bl_provider_invoice_id BIGINT NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, admin_created BOOLEAN DEFAULT NULL, due_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, invoice_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status INT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, number VARCHAR(150) DEFAULT NULL, name VARCHAR(200) DEFAULT NULL, type VARCHAR(200) DEFAULT NULL, number_of_appointments INT DEFAULT NULL, number_of_calls INT DEFAULT NULL, po_number VARCHAR(200) DEFAULT NULL, revised_count INT DEFAULT NULL, invoiced_id INT DEFAULT NULL, total NUMERIC(19, 2) DEFAULT NULL, export_state_id VARCHAR(255) DEFAULT NULL, invoice_terms_id VARCHAR(255) DEFAULT NULL, quick_books_id VARCHAR(255) DEFAULT NULL, invoiced_image_key VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E889AF96EF1CA3B8 ON bl_provider_invoices (bl_provider_invoice_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE bl_provider_invoice_id_sequence CASCADE');
		$this->addSql('DROP TABLE bl_provider_invoices');
	}
}
