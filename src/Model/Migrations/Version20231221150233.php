<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231221150233 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add xtrf9.9 missing columns';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('ALTER TABLE workflow_job ADD job_offers_type VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE workflow_job ADD job_prospecting_strategy_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE workflow_job ADD automatically_start_job_after_vendor_selection BOOLEAN DEFAULT false NOT NULL');
		$this->addSql('ALTER TABLE workflow_job_file DROP job_offers_type');
		$this->addSql('ALTER TABLE workflow_job_file DROP job_prospecting_strategy_id');
		$this->addSql('ALTER TABLE workflow_job_file DROP automatically_start_job_after_vendor_selection');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE project DROP working_days');
		$this->addSql('ALTER TABLE project ADD working_days INT DEFAULT NULL');
		$this->addSql('ALTER TABLE project DROP process_template_type');
		$this->addSql('ALTER TABLE project ALTER customer_id SET NOT NULL');
		$this->addSql('ALTER TABLE project ALTER sales_person_id SET NOT NULL');
		$this->addSql('ALTER TABLE workflow_job DROP job_offers_type');
		$this->addSql('ALTER TABLE workflow_job DROP job_prospecting_strategy_id');
		$this->addSql('ALTER TABLE workflow_job DROP automatically_start_job_after_vendor_selection');
		$this->addSql('ALTER TABLE quote DROP process_template_type');
		$this->addSql('ALTER TABLE quote ALTER customer_id SET NOT NULL');
		$this->addSql('ALTER TABLE provider_person DROP number_of_activities');
	}
}
