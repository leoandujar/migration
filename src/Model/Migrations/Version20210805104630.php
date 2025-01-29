<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210805104630 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE cp_setting_invoice_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cp_setting_invoice (cp_setting_invoice_id BIGINT NOT NULL, online_payment BOOLEAN DEFAULT \'false\' NOT NULL,  PRIMARY KEY(cp_setting_invoice_id))');
		$this->addSql('CREATE INDEX IDX_E863D8B33C08FA24 ON cp_setting_invoice (cp_setting_invoice_id)');
		$this->addSql('ALTER TABLE cp_setting ADD cp_setting_invoice_id BIGINT');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_A7E7955C5C05FA64 FOREIGN KEY (cp_setting_invoice_id) REFERENCES cp_setting_invoice (cp_setting_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A7E7613C5C03FA61 ON cp_setting (cp_setting_invoice_id)');
		$this->addSql('CREATE SEQUENCE cp_setting_quote_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cp_setting_quote (cp_setting_quote_id BIGINT NOT NULL, working_files_as_ref_files BOOLEAN DEFAULT \'true\' NOT NULL, update_working_files BOOLEAN DEFAULT \'true\' NOT NULL, confirmation_send_by_default BOOLEAN DEFAULT \'false\' NOT NULL, download_confirmation BOOLEAN DEFAULT \'true\' NOT NULL, deadline_options VARCHAR(255) NOT NULL, deadline_options_values VARCHAR(255) DEFAULT NULL, specialization_required BOOLEAN DEFAULT \'true\' NOT NULL, quick_estimate BOOLEAN DEFAULT \'false\' NOT NULL, deep_file_metrics BOOLEAN DEFAULT \'false\' NOT NULL, custom_fields JSON DEFAULT NULL, PRIMARY KEY(cp_setting_quote_id))');
		$this->addSql('CREATE INDEX IDX_E773D8B53C08FA24 ON cp_setting_quote (cp_setting_quote_id)');
		$this->addSql('ALTER TABLE cp_setting ADD cp_setting_quote_id BIGINT');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_B7E7967C2C05FA46 FOREIGN KEY (cp_setting_quote_id) REFERENCES cp_setting_quote (cp_setting_quote_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_B7E7621C6C03FA41 ON cp_setting (cp_setting_quote_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT FK_A7E7955C5C05FA64');
		$this->addSql('DROP INDEX UNIQ_A7E7613C5C03FA61');
		$this->addSql('ALTER TABLE cp_setting DROP cp_setting_invoice_id');
		$this->addSql('DROP SEQUENCE cp_setting_invoice_id_sequence CASCADE');
		$this->addSql('DROP TABLE cp_setting_invoice');
		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT FK_B7E7967C2C05FA46');
		$this->addSql('DROP INDEX UNIQ_B7E7621C6C03FA41');
		$this->addSql('ALTER TABLE cp_setting DROP cp_setting_quote_id');
		$this->addSql('DROP SEQUENCE cp_setting_quote_id_sequence CASCADE');
		$this->addSql('DROP TABLE cp_setting_quote');
	}
}
