<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017220224 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_workflow_monitor DROP CONSTRAINT fk_2c79cc7fde6cfcb2');
		$this->addSql('DROP INDEX idx_2c79cc7fde6cfcb2');
		$this->addSql('ALTER TABLE ap_workflow_monitor DROP additional_params');
		$this->addSql('ALTER TABLE ap_workflow_monitor RENAME COLUMN invoices_to_process TO auxiliary_data');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_workflow_monitor ADD additional_params INT DEFAULT NULL');
		$this->addSql('ALTER TABLE ap_workflow_monitor RENAME COLUMN auxiliary_data TO invoices_to_process');
		$this->addSql('ALTER TABLE ap_workflow_monitor ADD CONSTRAINT fk_2c79cc7fde6cfcb2 FOREIGN KEY (additional_params) REFERENCES wf_params (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_2c79cc7fde6cfcb2 ON ap_workflow_monitor (additional_params)');
	}
}
