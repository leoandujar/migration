<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220726223750 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE ap_workflow_monitor_id_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE ap_workflow_monitor (ap_workflow_monitor_id BIGINT NOT NULL, created_by BIGINT NOT NULL, workflow BIGINT NOT NULL, status INT NOT NULL, ordered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, finished_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, details JSON DEFAULT NULL, PRIMARY KEY(ap_workflow_monitor_id))');
		$this->addSql('CREATE INDEX IDX_2C79CC7FDE12AB56 ON ap_workflow_monitor (created_by)');
		$this->addSql('CREATE INDEX IDX_2C79CC7F65C59816 ON ap_workflow_monitor (workflow)');
		$this->addSql('CREATE INDEX IDX_2C79CC7F777ABBAB ON ap_workflow_monitor (ap_workflow_monitor_id)');
		$this->addSql('ALTER TABLE ap_workflow_monitor ADD CONSTRAINT FK_2C79CC7FDE12AB56 FOREIGN KEY (created_by) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE ap_workflow_monitor ADD CONSTRAINT FK_2C79CC7F65C59816 FOREIGN KEY (workflow) REFERENCES wf_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE ap_workflow_monitor_id_id_sequence CASCADE');
		$this->addSql('DROP TABLE ap_workflow_monitor');
	}
}
