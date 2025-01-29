<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200331220511 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE activity DROP vendor_evaluation_responsiveness');
		$this->addSql('ALTER TABLE activity DROP vendor_evaluation_on_time_delivery');
		$this->addSql('ALTER TABLE activity DROP vendor_evaluation_instructions');
		$this->addSql('ALTER TABLE activity DROP vendor_evaluation_collaboration');

		$this->addSql('ALTER TABLE customer DROP contract_end_date');
		$this->addSql('ALTER TABLE customer DROP kaiser_regional');
		$this->addSql('ALTER TABLE customer DROP account_type_sales');

		$this->addSql('ALTER TABLE project DROP li_provider_number');

		$this->addSql('ALTER TABLE activity ADD provider_evaluation_responsiveness TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD provider_evaluation_on_time_delivery TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD provider_evaluation_collaboration TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD provider_evaluation_instructions TEXT DEFAULT NULL');

		$this->addSql('ALTER TABLE customer ADD turnaround TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD sales_target_persona_a TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD sales_target_persona_b TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD sales_target_persona_c TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD contract_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD end_date_contract TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD kp_region VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD am_assigned VARCHAR(255) DEFAULT NULL');

		$this->addSql('ALTER TABLE customer ALTER sales_cut_off_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE customer ALTER sales_cut_off_date DROP DEFAULT');

		$this->addSql('ALTER TABLE project ADD li_provider_name TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE project RENAME COLUMN priority TO rush');

		$this->addSql('ALTER TABLE provider ADD xtm_uid TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_total_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD ref_date_2 TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_responsiveness_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_ontime_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_collaboration_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_instructions_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_quarter_total_average VARCHAR(255) DEFAULT NULL');

		$this->addSql('ALTER TABLE quote ADD quote_address TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ALTER li_provider_name TYPE TEXT');
		$this->addSql('ALTER TABLE quote ALTER li_provider_name DROP DEFAULT');
		$this->addSql('ALTER TABLE quote ALTER li_provider_name TYPE TEXT');

		$this->addSql('ALTER TABLE xtrf_user ADD owner_sf_id TEXT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE provider DROP xtm_uid');
		$this->addSql('ALTER TABLE provider DROP evaluation_total_average');
		$this->addSql('ALTER TABLE provider DROP ref_date_2');
		$this->addSql('ALTER TABLE provider DROP evaluation_responsiveness_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_ontime_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_collaboration_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_instructions_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_quarter_total_average');
		$this->addSql('ALTER TABLE xtrf_user DROP owner_sf_id');
		$this->addSql('ALTER TABLE quote DROP quote_address');
		$this->addSql('ALTER TABLE quote ALTER li_provider_name TYPE VARCHAR(1000)');
		$this->addSql('ALTER TABLE quote ALTER li_provider_name DROP DEFAULT');
		$this->addSql('ALTER TABLE customer ADD contract_end_date DATE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD kaiser_regional VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD account_type_sales VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE customer DROP turnaround');
		$this->addSql('ALTER TABLE customer DROP sales_target_persona_a');
		$this->addSql('ALTER TABLE customer DROP sales_target_persona_b');
		$this->addSql('ALTER TABLE customer DROP sales_target_persona_c');
		$this->addSql('ALTER TABLE customer DROP contract_amount');
		$this->addSql('ALTER TABLE customer DROP end_date_contract');
		$this->addSql('ALTER TABLE customer DROP kp_region');
		$this->addSql('ALTER TABLE customer DROP am_assigned');
		$this->addSql('ALTER TABLE customer ALTER sales_cut_off_date TYPE DATE');
		$this->addSql('ALTER TABLE customer ALTER sales_cut_off_date DROP DEFAULT');
		$this->addSql('ALTER TABLE project ADD li_provider_number VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE project DROP li_provider_name');
		$this->addSql('ALTER TABLE project RENAME COLUMN rush TO priority');
		$this->addSql('ALTER TABLE activity ADD vendor_evaluation_responsiveness INT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD vendor_evaluation_on_time_delivery INT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD vendor_evaluation_instructions INT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD vendor_evaluation_collaboration INT DEFAULT NULL');
		$this->addSql('ALTER TABLE activity DROP provider_evaluation_responsiveness');
		$this->addSql('ALTER TABLE activity DROP provider_evaluation_on_time_delivery');
		$this->addSql('ALTER TABLE activity DROP provider_evaluation_collaboration');
		$this->addSql('ALTER TABLE activity DROP provider_evaluation_instructions');
	}
}
