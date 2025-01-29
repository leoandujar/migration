<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221011125550 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE activity_type ADD qbo_item_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE activity_type ADD CONSTRAINT FK_8F1A8CBB1E6CAAD FOREIGN KEY (qbo_item_id) REFERENCES qbo_item (qbo_item_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_8F1A8CBB1E6CAAD ON activity_type (qbo_item_id)');
		$this->addSql('ALTER TABLE ap_workflow_monitor ADD additional_params INT DEFAULT NULL');
		$this->addSql('ALTER TABLE ap_workflow_monitor ADD CONSTRAINT FK_2C79CC7FDE6CFCB2 FOREIGN KEY (additional_params) REFERENCES wf_params (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_2C79CC7FDE6CFCB2 ON ap_workflow_monitor (additional_params)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_workflow_monitor DROP CONSTRAINT FK_2C79CC7FDE6CFCB2');
		$this->addSql('DROP INDEX IDX_2C79CC7FDE6CFCB2');
		$this->addSql('ALTER TABLE ap_workflow_monitor DROP additional_params');
		$this->addSql('ALTER TABLE activity_type DROP CONSTRAINT FK_8F1A8CBB1E6CAAD');
		$this->addSql('DROP INDEX IDX_8F1A8CBB1E6CAAD');
		$this->addSql('ALTER TABLE activity_type DROP qbo_item_id');
	}
}
