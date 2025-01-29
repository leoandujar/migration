<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818200422 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_deal ADD days_to_close INT DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_closed_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_deal_stage_probability VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_forecast_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_is_closed VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_is_closed_won VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_latest_meeting_activity VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_num_target_accounts VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_projected_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_sales_email_last_replied VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD notes_last_contacted VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD num_associated_contacts VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD num_notes VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD services_requested VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD copies_of_all_bids_received VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD go_no_go_score VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_acv VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_analytics_source VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_analytics_source_data_1 VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_analytics_source_data_2 VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_campaign VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_deal_amount_calculation_preference VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_deal_stage_probability_shadow VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_forecast_probability VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_likelihood_to_close VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_manual_forecast_category VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_mrr VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_next_step VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_num_associated_deal_splits VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_predicted_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_priority VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD hs_tcv VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD reason_for_no_bid VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD successful_bidder VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD engagements_last_meeting_booked VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD engagements_last_meeting_booked_medium VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD id_quotes VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD closed_won_reason VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD annual_contract_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD excited_for_this_bid VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD contract_term_ending_date VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD go_hs_generated VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD go_score VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD initial_caller_client_services_member VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD num_times_contacted VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal ADD opportunity_ratio VARCHAR(255) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_deal DROP days_to_close');
		$this->addSql('ALTER TABLE hs_deal DROP hs_closed_amount');
		$this->addSql('ALTER TABLE hs_deal DROP hs_deal_stage_probability');
		$this->addSql('ALTER TABLE hs_deal DROP hs_forecast_amount');
		$this->addSql('ALTER TABLE hs_deal DROP hs_is_closed');
		$this->addSql('ALTER TABLE hs_deal DROP hs_is_closed_won');
		$this->addSql('ALTER TABLE hs_deal DROP hs_latest_meeting_activity');
		$this->addSql('ALTER TABLE hs_deal DROP hs_num_target_accounts');
		$this->addSql('ALTER TABLE hs_deal DROP hs_projected_amount');
		$this->addSql('ALTER TABLE hs_deal DROP hs_sales_email_last_replied');
		$this->addSql('ALTER TABLE hs_deal DROP notes_last_contacted');
		$this->addSql('ALTER TABLE hs_deal DROP num_associated_contacts');
		$this->addSql('ALTER TABLE hs_deal DROP num_notes');
		$this->addSql('ALTER TABLE hs_deal DROP services_requested');
		$this->addSql('ALTER TABLE hs_deal DROP copies_of_all_bids_received');
		$this->addSql('ALTER TABLE hs_deal DROP go_no_go_score');
		$this->addSql('ALTER TABLE hs_deal DROP hs_acv');
		$this->addSql('ALTER TABLE hs_deal DROP hs_analytics_source');
		$this->addSql('ALTER TABLE hs_deal DROP hs_analytics_source_data_1');
		$this->addSql('ALTER TABLE hs_deal DROP hs_analytics_source_data_2');
		$this->addSql('ALTER TABLE hs_deal DROP hs_campaign');
		$this->addSql('ALTER TABLE hs_deal DROP hs_deal_amount_calculation_preference');
		$this->addSql('ALTER TABLE hs_deal DROP hs_deal_stage_probability_shadow');
		$this->addSql('ALTER TABLE hs_deal DROP hs_forecast_probability');
		$this->addSql('ALTER TABLE hs_deal DROP hs_likelihood_to_close');
		$this->addSql('ALTER TABLE hs_deal DROP hs_manual_forecast_category');
		$this->addSql('ALTER TABLE hs_deal DROP hs_mrr');
		$this->addSql('ALTER TABLE hs_deal DROP hs_next_step');
		$this->addSql('ALTER TABLE hs_deal DROP hs_num_associated_deal_splits');
		$this->addSql('ALTER TABLE hs_deal DROP hs_predicted_amount');
		$this->addSql('ALTER TABLE hs_deal DROP hs_priority');
		$this->addSql('ALTER TABLE hs_deal DROP hs_tcv');
		$this->addSql('ALTER TABLE hs_deal DROP reason_for_no_bid');
		$this->addSql('ALTER TABLE hs_deal DROP successful_bidder');
		$this->addSql('ALTER TABLE hs_deal DROP engagements_last_meeting_booked');
		$this->addSql('ALTER TABLE hs_deal DROP engagements_last_meeting_booked_medium');
		$this->addSql('ALTER TABLE hs_deal DROP id_quotes');
		$this->addSql('ALTER TABLE hs_deal DROP closed_won_reason');
		$this->addSql('ALTER TABLE hs_deal DROP annual_contract_amount');
		$this->addSql('ALTER TABLE hs_deal DROP excited_for_this_bid');
		$this->addSql('ALTER TABLE hs_deal DROP contract_term_ending_date');
		$this->addSql('ALTER TABLE hs_deal DROP go_hs_generated');
		$this->addSql('ALTER TABLE hs_deal DROP go_score');
		$this->addSql('ALTER TABLE hs_deal DROP initial_caller_client_services_member');
		$this->addSql('ALTER TABLE hs_deal DROP num_times_contacted');
		$this->addSql('ALTER TABLE hs_deal DROP opportunity_ratio');
	}
}
