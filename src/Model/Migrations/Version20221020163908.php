<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020163908 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('DROP TABLE IF EXISTS metrics_progress');
		$this->addSql('DROP TABLE IF EXISTS metrics');
		$this->addSql('DROP TABLE IF EXISTS time_serie_stats');
		$this->addSql('DROP TABLE IF EXISTS authentication_history');

		$this->addSql('ALTER TABLE action RENAME COLUMN id TO action_id');
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AA9D32F035');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA9D32F035 FOREIGN KEY (action_id) REFERENCES action (action_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE role RENAME COLUMN id TO role_id');
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT fk_e04992aad60322ac');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT fk_e04992aad60322ac FOREIGN KEY (role_id) REFERENCES role (role_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE alert RENAME COLUMN id TO alert_id');

		$this->addSql('ALTER TABLE ap_template RENAME COLUMN cp_template_id TO ap_template_id');
		$this->addSql('CREATE SEQUENCE ap_template_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('DROP INDEX IF EXISTS idx_533d33c1bb41d498');
		$this->addSql('CREATE INDEX idx_533d33c1bb41d498 ON ap_template (ap_template_id)');

		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_call_id TO blcall_id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN id TO bl_call_id');
		$this->addSql('alter table bl_call drop constraint bl_call_pkey cascade');
		$this->addSql('alter table bl_call add primary key (bl_call_id)');
		$this->addSql('ALTER TABLE bl_call ALTER customer_duration SET NOT NULL');
		$this->addSql('ALTER TABLE bl_call ALTER duration_minimal SET NOT NULL');

		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_translation_type_id TO bltranslation_type_id');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN id TO bl_translation_type_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_ffd0944387636b9c');
		$this->addSql('CREATE UNIQUE INDEX uniq_ffd0944387636b9c ON bl_translation_type (bltranslation_type_id)');
		$this->addSql('alter table bl_translation_type drop constraint bl_translation_type_pkey');
		$this->addSql('alter table bl_translation_type add primary key (bl_translation_type_id)');

		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN bl_communication_type_id TO blcommunication_type_id');
		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN id TO bl_communication_type_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_f31cc075eb80e479 cascade ');
		$this->addSql('CREATE UNIQUE INDEX uniq_f31cc075eb80e479 ON bl_communication_type (blcommunication_type_id)');
		$this->addSql('alter table bl_communication_type drop constraint bl_communication_type_pkey CASCADE ');
		$this->addSql('alter table bl_communication_type add primary key (bl_communication_type_id)');

		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN bl_contact_id TO blcontact_id');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN id TO bl_contact_id');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5aba3ee6cc0');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5aba3ee6cc0 FOREIGN KEY (bl_contact_id) REFERENCES bl_contact (bl_contact_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('alter table bl_contact drop constraint bl_contact_pkey cascade ');
		$this->addSql('alter table bl_contact add primary key (bl_contact_id)');

		$this->addSql('ALTER TABLE bl_customer RENAME COLUMN bl_customer_id TO blcustomer_id');
		$this->addSql('ALTER TABLE bl_customer RENAME COLUMN id TO bl_customer_id');
		$this->addSql('DROP INDEX IF EXISTS idx_e114a52c9395c3f3');
		$this->addSql('DROP INDEX IF EXISTS UNIQ_E114A52C9395C3F3');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E114A52C9395C3F3 ON bl_customer (customer_id)');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5ab9ebce684');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab9ebce684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (bl_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('alter table bl_customer drop constraint bl_customer_pkey cascade');
		$this->addSql('alter table bl_customer add primary key (bl_customer_id)');

		$this->addSql('ALTER TABLE bl_language RENAME COLUMN bl_language_id TO bllanguage_id');
		$this->addSql('ALTER TABLE bl_language RENAME COLUMN id TO bl_language_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_b4f65a908929b857');
		$this->addSql('CREATE UNIQUE INDEX uniq_b4f65a908929b857 ON bl_language (bllanguage_id)');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5ab45de1a76');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab45de1a76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5abfe9b05dc');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abfe9b05dc FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('alter table bl_language drop constraint bl_language_pkey cascade');
		$this->addSql('alter table bl_language add primary key (bl_language_id)');

		$this->addSql('ALTER TABLE bl_provider_invoice RENAME COLUMN bl_provider_invoice_id TO blprovider_invoice_id');
		$this->addSql('ALTER TABLE bl_provider_invoice RENAME COLUMN id TO bl_provider_invoice_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_e889af96ef1ca3b8');
		$this->addSql('CREATE UNIQUE INDEX uniq_e889af96ef1ca3b8 ON bl_provider_invoice (blprovider_invoice_id)');
		$this->addSql('alter table bl_provider_invoice drop constraint IF EXISTS bl_provider_invoice_pkey cascade');
		$this->addSql('alter table bl_provider_invoice drop constraint IF EXISTS bl_provider_invoices_pkey cascade');
		$this->addSql('alter table bl_provider_invoice add primary key (bl_provider_invoice_id)');

		$this->addSql('ALTER TABLE bl_rate RENAME COLUMN bl_rate_id TO blrate_id');
		$this->addSql('ALTER TABLE bl_rate RENAME COLUMN id TO bl_rate_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_ba98f5ac213c6f00');
		$this->addSql('CREATE UNIQUE INDEX uniq_ba98f5ac213c6f00 ON bl_rate (blrate_id)');
		$this->addSql('alter table bl_rate drop constraint bl_rate_pkey cascade');
		$this->addSql('alter table bl_rate add primary key (bl_rate_id)');

		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN bl_service_type_id TO blservice_type_id');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN id TO bl_service_type_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_8cf12e7ff8512177');
		$this->addSql('CREATE UNIQUE INDEX uniq_8cf12e7ff8512177 ON bl_service_type (blservice_type_id)');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5abf8512177');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abf8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (bl_service_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('alter table bl_service_type drop constraint bl_service_type_pkey cascade');
		$this->addSql('alter table bl_service_type add primary key (bl_service_type_id)');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5abeb80e479');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abeb80e479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (bl_communication_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_contact_person RENAME COLUMN hs_contact_person_id TO hscontact_person_id');
		$this->addSql('ALTER TABLE hs_contact_person RENAME COLUMN id TO hs_contact_person_id');
		$this->addSql('alter table hs_contact_person drop constraint hs_contact_person_pkey cascade');
		$this->addSql('alter table hs_contact_person add primary key (hs_contact_person_id)');

		$this->addSql('ALTER TABLE hs_customer RENAME COLUMN hs_customer_id TO hscustomer_id');
		$this->addSql('ALTER TABLE hs_customer RENAME COLUMN id TO hs_customer_id');
		$this->addSql('ALTER TABLE hs_customer DROP CONSTRAINT IF EXISTS fk_d51e27cde5c3ed04');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT fk_d51e27cde5c3ed04 FOREIGN KEY (hs_parent_customer_id) REFERENCES hs_customer (hs_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('alter table hs_customer drop constraint hs_customer_pkey cascade');
		$this->addSql('alter table hs_customer add primary key (hs_customer_id)');

		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN hs_deal_id TO hsdeal_id');
		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN id TO hs_deal_id');
		$this->addSql('alter table hs_deal drop constraint hs_deal_pkey cascade');
		$this->addSql('alter table hs_deal add primary key (hs_deal_id)');

		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT IF EXISTS fk_13f52bdc970747f7');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT fk_13f52bdc970747f7 FOREIGN KEY (hs_company_id) REFERENCES hs_customer (hs_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT IF EXISTS fk_13f52bdce73d786b');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT fk_13f52bdce73d786b FOREIGN KEY (hs_contact_id) REFERENCES hs_contact_person (hs_contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT IF EXISTS fk_13f52bdcff0cef3');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT fk_13f52bdcff0cef3 FOREIGN KEY (hs_deal_id) REFERENCES hs_deal (hs_deal_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_marketing_email RENAME COLUMN hs_marketing_email_id TO hsmarketing_email_id');
		$this->addSql('ALTER TABLE hs_marketing_email RENAME COLUMN id TO hs_marketing_email_id');
		$this->addSql('alter table hs_marketing_email drop constraint hs_marketing_email_pkey cascade');
		$this->addSql('alter table hs_marketing_email add primary key (hs_marketing_email_id)');

		$this->addSql('ALTER TABLE permission RENAME COLUMN id TO permission_id');

		$this->addSql('ALTER TABLE wf_history RENAME COLUMN id TO wf_history_id');

		$this->addSql('ALTER TABLE wf_params RENAME COLUMN id TO wf_params_id');
		$this->addSql('ALTER TABLE wf_params ALTER COLUMN wf_params_id TYPE bigint');

		$this->addSql('ALTER TABLE wf_workflow RENAME COLUMN id TO wf_workflow_id');

		$this->addSql('ALTER TABLE av_report_chart RENAME COLUMN id TO av_report_chart_id');
		$this->addSql('ALTER TABLE av_report_chart ALTER category DROP NOT NULL');
		$this->addSql('ALTER TABLE av_report_chart ALTER category TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE av_report_chart DROP CONSTRAINT IF EXISTS FK_A4DE6C67FFF2BAD2');
		$this->addSql('ALTER TABLE av_report_chart ADD CONSTRAINT FK_A4DE6C67FFF2BAD2 FOREIGN KEY (report_type) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE av_piv_reports_templates_charts DROP CONSTRAINT FK_DB1F62A0BEF83E0A');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ADD CONSTRAINT FK_DB1F62A0BEF83E0A FOREIGN KEY (chart_id) REFERENCES av_report_chart (av_report_chart_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE av_category_group RENAME COLUMN id TO av_category_group_id');
		$this->addSql('ALTER TABLE chart_group RENAME COLUMN id TO chart_group_id');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT FK_C118DCC08BF1FB99');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT FK_C118DCC08BF1FB99 FOREIGN KEY (chart_group) REFERENCES av_category_group (av_category_group_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE token RENAME COLUMN id TO cp_token_id');
		$this->addSql('ALTER TABLE token RENAME TO cp_token');

		$this->addSql('ALTER TABLE lqa_issue RENAME COLUMN id TO lqa_issue_id');

		$this->addSql('ALTER TABLE lqa_issue_type RENAME COLUMN id TO lqa_issue_type_id');
		$this->addSql('ALTER TABLE lqa_issue_type_mapping RENAME COLUMN id TO lqa_issue_type_mapping_id');

		$this->addSql('ALTER TABLE parameter RENAME COLUMN id TO parameter_id');

		$this->addSql('ALTER TABLE xtm_edit_distance RENAME COLUMN id TO xtm_edit_distance_id');

		$this->addSql('ALTER TABLE xtm_metrics RENAME COLUMN id TO xtm_metrics_id');

		$this->addSql('ALTER TABLE xtm_statistics RENAME COLUMN id TO xtm_statistics_id');

		$this->addSql('ALTER TABLE analytics_project_step ALTER ordinal TYPE SMALLINT');
		$this->addSql('ALTER TABLE analytics_project_step ALTER ordinal DROP DEFAULT');

		$this->addSql('ALTER TABLE branch ADD IF NOT EXISTS use_default_client_portal_background BOOLEAN DEFAULT \'true\' NOT NULL');
		$this->addSql('ALTER TABLE branch ADD IF NOT EXISTS use_default_client_portal_favicon BOOLEAN DEFAULT \'true\' NOT NULL');
		$this->addSql('ALTER TABLE branch DROP IF EXISTS use_default_customer_portal_background');
		$this->addSql('ALTER TABLE branch DROP IF EXISTS use_default_customer_portal_favicon');
		$this->addSql('ALTER TABLE cp_setting_quote ALTER deadline_options DROP DEFAULT');
		$this->addSql('ALTER TABLE cp_setting_quote ALTER deadline_options TYPE JSON USING (deadline_options::json)');

		$this->addSql('ALTER TABLE cp_template ALTER name TYPE VARCHAR(50)');

		$this->addSql('ALTER TABLE customer_invoice ALTER credit_note_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE customer_invoice ALTER credit_note_date DROP DEFAULT');

		$this->addSql('ALTER TABLE customer_invoice_item ALTER original_item_id TYPE INT');
		$this->addSql('ALTER TABLE customer_invoice_item ALTER original_item_id DROP DEFAULT');

		$this->addSql('DROP INDEX IF EXISTS idx_2fb3d0eebkl1ab5d3');

		$this->addSql('ALTER TABLE provider_person ALTER invitation_sent DROP DEFAULT');

		$this->addSql('ALTER TABLE quality_category ALTER type DROP DEFAULT');

		$this->addSql('ALTER TABLE quality_report DROP CONSTRAINT IF EXISTS fk_606ff4b6ac74095a');
		$this->addSql('ALTER TABLE quality_report ALTER type DROP DEFAULT');
		$this->addSql('ALTER TABLE quality_report ALTER comment SET DEFAULT \'\'');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT FK_C118DCC0E5562A2A');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT FK_C118DCC0E5562A2A FOREIGN KEY (chart) REFERENCES av_report_chart (av_report_chart_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS FK_A9FAE5ABA3EE6CC0');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABA3EE6CC0 FOREIGN KEY (bl_contact_id) REFERENCES bl_contact (bl_contact_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS FK_A9FAE5ABFE9B05DC');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABFE9B05DC FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS FK_A9FAE5AB45DE1A76');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB45DE1A76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS FK_A9FAE5AB9EBCE684');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB9EBCE684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (bl_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS FK_A9FAE5AB213C6F00');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB213C6F00 FOREIGN KEY (bl_rate_id) REFERENCES bl_rate (bl_rate_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('DROP INDEX IF EXISTS UNIQ_F31CC075EB80E479');
		$this->addSql('CREATE INDEX IF NOT EXISTS IDX_F31CC075CEB38643 ON bl_communication_type (blcommunication_type_id)');
		$this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_F31CC075EB80E479 ON bl_communication_type (bl_communication_type_id)');

		$this->addSql('ALTER TABLE bl_contact DROP CONSTRAINT IF EXISTS FK_D1C716A79EBCE684');
		$this->addSql('ALTER TABLE bl_contact ADD CONSTRAINT FK_D1C716A79EBCE684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (bl_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_B4F65A90A5F50306 ON bl_language (bllanguage_id)');

		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT IF EXISTS FK_FFD09443EB80E479');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT FK_FFD09443EB80E479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (bl_communication_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_customer DROP CONSTRAINT IF EXISTS FK_D51E27CDE5C3ED04');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CDE5C3ED04 FOREIGN KEY (hs_parent_customer_id) REFERENCES hs_customer (hs_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE lqa_issue DROP CONSTRAINT IF EXISTS FK_3832DE408EB20258');
		$this->addSql('ALTER TABLE lqa_issue ADD CONSTRAINT FK_3832DE408EB20258 FOREIGN KEY (lqa_issue_type_mapping_id) REFERENCES lqa_issue_type_mapping (lqa_issue_type_mapping_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE lqa_issue_type DROP CONSTRAINT IF EXISTS FK_7FF33CD4727ACA70');
		$this->addSql('ALTER TABLE lqa_issue_type ADD CONSTRAINT FK_7FF33CD4727ACA70 FOREIGN KEY (parent_id) REFERENCES lqa_issue_type (lqa_issue_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE wf_history ALTER wf_history_id DROP DEFAULT');

		$this->addSql('ALTER TABLE wf_params DROP CONSTRAINT FK_8FDE25802C7C2CBA');
		$this->addSql('ALTER TABLE wf_params ADD CONSTRAINT FK_8FDE25802C7C2CBA FOREIGN KEY (workflow_id) REFERENCES wf_workflow (wf_workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE wf_workflow ALTER wf_workflow_id DROP DEFAULT');

		$this->addSql('ALTER INDEX IF EXISTS idx_533d33c1bb41d498 RENAME TO IDX_533D33C15A2F1037');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ALTER chart_id SET NOT NULL');
		$this->addSql('ALTER INDEX IF EXISTS idx_1f7156cb5da0fb8 RENAME TO IDX_DB1F62A05DA0FB8');
		$this->addSql('ALTER INDEX IF EXISTS uniq_2877fd485e237e06 RENAME TO UNIQ_A4DE6C675E237E06');
		$this->addSql('ALTER INDEX IF EXISTS idx_e5562a2afff2bad2 RENAME TO IDX_A4DE6C67FFF2BAD2');
		$this->addSql('ALTER INDEX IF EXISTS uniq_e114a52c9ebce684 RENAME TO UNIQ_E114A52CB4917A01');
		$this->addSql('DROP INDEX IF EXISTS uniq_b4f65a908929b857');
		$this->addSql('DROP INDEX IF EXISTS uniq_b4f65a908fd89f83');
		$this->addSql('ALTER INDEX IF EXISTS uniq_e889af96ef1ca3b8 RENAME TO UNIQ_3E11D512ECEC1505');
		$this->addSql('ALTER INDEX IF EXISTS uniq_ba98f5ac213c6f00 RENAME TO UNIQ_BA98F5AC322D4901');
		$this->addSql('DROP INDEX IF EXISTS uniq_8cf12e7ff8512178 cascade ');
		$this->addSql('ALTER INDEX IF EXISTS uniq_8cf12e7ff8512177 RENAME TO UNIQ_8CF12E7F2D4EB5D5');
		$this->addSql('ALTER INDEX IF EXISTS uniq_ffd0944387636b9c RENAME TO UNIQ_FFD094438493DD29');
		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT IF EXISTS fk_a7e7925c9395c3f3');
		$this->addSql('ALTER INDEX IF EXISTS uniq_b7e7621c6c03fa41 RENAME TO UNIQ_A7E7925CCE9DAC40');
		$this->addSql('ALTER INDEX IF EXISTS uniq_a7e7613c5c03fa61 RENAME TO UNIQ_A7E7925C3E71406');
		$this->addSql('ALTER INDEX IF EXISTS idx_e863d8b33c08fa24 RENAME TO IDX_57B51F193E71406');
		$this->addSql('ALTER INDEX IF EXISTS idx_e773d8b53c08fa24 RENAME TO IDX_A65CA9F0CE9DAC40');
		$this->addSql('ALTER INDEX IF EXISTS idx_5f37a13ba76ed395 RENAME TO IDX_B3E3441EA76ED396');
		$this->addSql('ALTER INDEX IF EXISTS idx_418857d79395c3f3 RENAME TO IDX_2B9B5FC19395C3F4');
		$this->addSql('ALTER INDEX IF EXISTS idx_418857d760984f51 RENAME TO IDX_2B9B5FC160984F52');
		$this->addSql('ALTER INDEX IF EXISTS idx_418857d7dda81d53 RENAME TO IDX_2B9B5FC1E304DE5');
		$this->addSql('ALTER INDEX IF EXISTS idx_9f8d3b6a727aca70 RENAME TO IDX_122C91B5727ACA71');
		$this->addSql('ALTER INDEX IF EXISTS idx_9f8d3b6a8cba9bfc RENAME TO IDX_122C91B5FF562E62');
		$this->addSql('ALTER INDEX IF EXISTS idx_83ea7c58a4f1f82 RENAME TO IDX_603EB0D1CAE716F');
		$this->addSql('ALTER INDEX IF EXISTS idx_83ea7c588cba9bfc RENAME TO IDX_603EB0DFF562E62');
		$this->addSql('ALTER INDEX IF EXISTS idx_83ea7c58bf396750 RENAME TO IDX_603EB0DBF396751');
		$this->addSql('ALTER INDEX IF EXISTS idx_606ff4b6a4f1f82 RENAME TO IDX_7BEBB85A1CAE717D');
		$this->addSql('ALTER TABLE wf_params ALTER workflow_id SET NOT NULL');
		$this->addSql('DROP INDEX IF EXISTS idx_b75bbf606d9546f');

		$this->addSql('DROP INDEX IF EXISTS IDX_533D33C15A2F1037');
		$this->addSql('DROP INDEX IF EXISTS uniq_3e11d512ecec1505');
		$this->addSql('DROP INDEX IF EXISTS uniq_ba98f5ac322d4901');
		$this->addSql('DROP INDEX IF EXISTS uniq_8cf12e7f2d4eb5d5');
		$this->addSql('DROP INDEX IF EXISTS uniq_ffd094438493dd29');
		$this->addSql('ALTER INDEX IF EXISTS uniq_a7e7925cce9dac40 RENAME TO UNIQ_A7E7925CCE9DAC39');
		$this->addSql('ALTER INDEX IF EXISTS uniq_a7e7925c3e71406 RENAME TO UNIQ_A7E7925C3E71405');
		$this->addSql('ALTER INDEX IF EXISTS idx_57b51f193e71406 RENAME TO IDX_57B51F193E71405');
		$this->addSql('ALTER INDEX IF EXISTS idx_a65ca9f0ce9dac40 RENAME TO IDX_A65CA9F0CE9DAC39');
		$this->addSql('ALTER INDEX IF EXISTS idx_b3e3441ea76ed396 RENAME TO IDX_B3E3441EA76ED395');
		$this->addSql('ALTER INDEX IF EXISTS idx_2b9b5fc19395c3f4 RENAME TO IDX_2B9B5FC19395C3F3');
		$this->addSql('ALTER INDEX IF EXISTS idx_2b9b5fc160984f52 RENAME TO IDX_2B9B5FC160984F51');
		$this->addSql('ALTER INDEX IF EXISTS idx_2b9b5fc1e304de5 RENAME TO IDX_2B9B5FC1E304DE4');
		$this->addSql('ALTER INDEX IF EXISTS idx_122c91b5727aca71 RENAME TO IDX_122C91B5727ACA70');
		$this->addSql('ALTER INDEX IF EXISTS idx_122c91b5ff562e62 RENAME TO IDX_122C91B5FF562E61');
		$this->addSql('ALTER INDEX IF EXISTS idx_603eb0d1cae716f RENAME TO IDX_603EB0D1CAE716D');
		$this->addSql('ALTER INDEX IF EXISTS idx_603eb0dff562e62 RENAME TO IDX_603EB0DFF562E61');
		$this->addSql('ALTER INDEX IF EXISTS idx_603eb0dbf396751 RENAME TO IDX_603EB0DBF396750');
		$this->addSql('ALTER INDEX IF EXISTS idx_7bebb85a1cae717d RENAME TO IDX_7BEBB85A1CAE716D');

		$this->addSql('DROP INDEX IF EXISTS idx_533d33c15a2f1036');
		$this->addSql('DROP INDEX IF EXISTS uniq_3e11d512ecec1504');
		$this->addSql('DROP INDEX IF EXISTS uniq_ba98f5ac322d4900');
		$this->addSql('DROP INDEX IF EXISTS uniq_8cf12e7f2d4eb5d4');
		$this->addSql('DROP INDEX IF EXISTS uniq_ffd094438493dd28');

		$this->addSql('CREATE INDEX IF NOT EXISTS IDX_533D33C15A2F1035 ON ap_template (ap_template_id)');
		$this->addSql('ALTER TABLE av_report_chart DROP CONSTRAINT IF EXISTS fk_a4de6c67fff2bad2');
		$this->addSql('ALTER INDEX IF EXISTS uniq_3e11d512ef1ca3b8 RENAME TO UNIQ_3E11D512ECEC1503');
		$this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_BA98F5AC322D4899 ON bl_rate (blrate_id)');
		$this->addSql('ALTER TABLE bl_service_type DROP CONSTRAINT IF EXISTS bl_service_type_pk CASCADE ');
		$this->addSql('DROP INDEX IF EXISTS bl_service_type_pk CASCADE ');
		$this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_8CF12E7F2D4EB5D3 ON bl_service_type (blservice_type_id)');
		$this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_FFD094438493DD27 ON bl_translation_type (bltranslation_type_id)');
		$this->addSql('ALTER TABLE quality_report DROP CONSTRAINT IF EXISTS FK_A9FAE5ABF8512177');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABF8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (bl_service_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AA9D32F035');
		$this->addSql('ALTER TABLE action RENAME COLUMN action_id TO id');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA9D32F035 FOREIGN KEY (action_id) REFERENCES action (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE permission DROP CONSTRAINT fk_e04992aad60322ac');
		$this->addSql('ALTER TABLE role RENAME COLUMN role_id TO id');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT fk_e04992aad60322ac FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE alert RENAME COLUMN alert_id TO id');

		$this->addSql('DROP SEQUENCE ap_template_id_sequence CASCADE');
		$this->addSql('ALTER TABLE ap_template RENAME COLUMN ap_template_id TO cp_template_id');
		$this->addSql('DROP INDEX IF EXISTS  idx_533d33c1bb41d498');
		$this->addSql('CREATE INDEX idx_533d33c1bb41d498 ON ap_template (cp_template_id)');

		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_call_id TO id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN blcall_id TO bl_call_id');

		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN bl_communication_type_id TO id');
		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN blcommunication_type_id TO bl_communication_type_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_f31cc075eb80e479');
		$this->addSql('CREATE UNIQUE INDEX uniq_f31cc075eb80e479 ON bl_communication_type (bl_communication_type_id)');

		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN bl_contact_id TO id');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN blcontact_id TO bl_contact_id');

		$this->addSql('ALTER TABLE bl_customer RENAME COLUMN bl_customer_id TO id');
		$this->addSql('ALTER TABLE bl_customer RENAME COLUMN blcustomer_id TO bl_customer_id');
		$this->addSql('DROP INDEX IF EXISTS idx_e114a52c9395c3f3');
		$this->addSql('DROP INDEX IF EXISTS UNIQ_E114A52C9395C3F3');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E114A52C9395C3F3 ON bl_customer (id)');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5ab9ebce684');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab9ebce684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_contact DROP CONSTRAINT IF EXISTS fk_d1c716a79ebce684');
		$this->addSql('ALTER TABLE bl_contact ADD CONSTRAINT fk_d1c716a79ebce684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5aba3ee6cc0');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5aba3ee6cc0 FOREIGN KEY (bl_contact_id) REFERENCES bl_contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE bl_language RENAME COLUMN bl_language_id TO id');
		$this->addSql('ALTER TABLE bl_language RENAME COLUMN bllanguage_id TO bl_language_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_b4f65a908929b857');
		$this->addSql('CREATE UNIQUE INDEX uniq_b4f65a908929b857 ON bl_language (bl_language_id)');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5ab45de1a76');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab45de1a76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5abfe9b05dc');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abfe9b05dc FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE bl_provider_invoice RENAME COLUMN bl_provider_invoice_id TO id');
		$this->addSql('ALTER TABLE bl_provider_invoice RENAME COLUMN blprovider_invoice_id TO bl_provider_invoice_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_e889af96ef1ca3b8');
		$this->addSql('CREATE UNIQUE INDEX uniq_e889af96ef1ca3b8 ON bl_provider_invoice (bl_provider_invoice_id)');

		$this->addSql('ALTER TABLE bl_rate RENAME COLUMN bl_rate_id TO id');
		$this->addSql('ALTER TABLE bl_rate RENAME COLUMN blrate_id TO bl_rate_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_ba98f5ac213c6f00');
		$this->addSql('CREATE UNIQUE INDEX uniq_ba98f5ac213c6f00 ON bl_rate (bl_rate_id)');

		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN bl_service_type_id TO id');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN blservice_type_id TO bl_service_type_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_8cf12e7ff8512177');
		$this->addSql('CREATE UNIQUE INDEX uniq_8cf12e7ff8512177 ON bl_service_type (bl_service_type_id)');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5abf8512177');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abf8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_translation_type_id TO id');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bltranslation_type_id TO bl_translation_type_id');
		$this->addSql('DROP INDEX IF EXISTS uniq_ffd0944387636b9c');
		$this->addSql('CREATE UNIQUE INDEX uniq_ffd0944387636b9c ON bl_translation_type (bl_translation_type_id)');
		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT IF EXISTS fk_ffd09443eb80e479');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT fk_ffd09443eb80e479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT IF EXISTS fk_a9fae5abeb80e479');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abeb80e479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_contact_person RENAME COLUMN hs_contact_person_id TO id');
		$this->addSql('ALTER TABLE hs_contact_person RENAME COLUMN hscontact_person_id TO hs_contact_person_id');

		$this->addSql('ALTER TABLE hs_customer RENAME COLUMN hs_customer_id TO id');
		$this->addSql('ALTER TABLE hs_customer RENAME COLUMN hscustomer_id TO hs_customer_id');
		$this->addSql('ALTER TABLE hs_customer DROP CONSTRAINT IF EXISTS fk_d51e27cde5c3ed04');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT fk_d51e27cde5c3ed04 FOREIGN KEY (hs_parent_customer_id) REFERENCES hs_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN hs_deal_id TO id');
		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN hsdeal_id TO hs_deal_id');

		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT IF EXISTS fk_13f52bdc970747f7');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT fk_13f52bdc970747f7 FOREIGN KEY (hs_company_id) REFERENCES hs_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT IF EXISTS fk_13f52bdce73d786b');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT fk_13f52bdce73d786b FOREIGN KEY (hs_contact_id) REFERENCES hs_contact_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT IF EXISTS fk_13f52bdcff0cef3');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT fk_13f52bdcff0cef3 FOREIGN KEY (hs_deal_id) REFERENCES hs_deal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE hs_marketing_email RENAME COLUMN hs_marketing_email_id TO id');
		$this->addSql('ALTER TABLE hs_marketing_email RENAME COLUMN hsmarketing_email_id TO hs_marketing_email_id');

		$this->addSql('ALTER TABLE permission RENAME COLUMN permission_id TO id');

		$this->addSql('ALTER TABLE wf_history RENAME COLUMN wf_history_id TO id');

		$this->addSql('ALTER TABLE wf_params RENAME COLUMN wf_params_id TO id');

		$this->addSql('ALTER TABLE wf_workflow RENAME COLUMN wf_workflow_id TO id');

		$this->addSql('ALTER TABLE av_report_chart RENAME COLUMN av_report_chart_id TO id');

		$this->addSql('ALTER TABLE av_report_chart DROP CONSTRAINT IF EXISTS FK_A4DE6C67FFF2BAD2');
		$this->addSql('ALTER TABLE av_report_chart ADD CONSTRAINT FK_A4DE6C67FFF2BAD2 FOREIGN KEY (report_type) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE av_piv_reports_templates_charts DROP CONSTRAINT FK_DB1F62A0BEF83E0A');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ADD CONSTRAINT FK_DB1F62A0BEF83E0A FOREIGN KEY (chart_id) REFERENCES av_report_chart (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE chart_group RENAME COLUMN chart_group_id TO id');
		$this->addSql('ALTER TABLE av_category_group RENAME COLUMN av_category_group_id TO id');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT FK_C118DCC08BF1FB99');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT FK_C118DCC08BF1FB99 FOREIGN KEY (chart_group) REFERENCES av_category_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE cp_token RENAME TO token');
		$this->addSql('ALTER TABLE token RENAME COLUMN cp_token_id TO id');

		$this->addSql('ALTER TABLE lqa_issue RENAME COLUMN lqa_issue_id TO id');

		$this->addSql('ALTER TABLE lqa_issue_type RENAME COLUMN lqa_issue_type_id TO id');
		$this->addSql('ALTER TABLE lqa_issue_type_mapping RENAME COLUMN lqa_issue_type_mapping_id TO id');

		$this->addSql('ALTER TABLE parameter RENAME COLUMN parameter_id TO id');

		$this->addSql('ALTER TABLE xtm_edit_distance RENAME COLUMN xtm_edit_distance_id TO id');

		$this->addSql('ALTER TABLE xtm_metrics RENAME COLUMN xtm_metrics_id TO id');

		$this->addSql('ALTER TABLE xtm_statistics RENAME COLUMN xtm_statistics_id TO id');

		$this->addSql('ALTER INDEX IF EXISTS  IDX_533D33C15A2F1037 RENAME TO idx_533d33c1bb41d498');
		$this->addSql('ALTER INDEX IF EXISTS IDX_DB1F62A05DA0FB8  RENAME TO idx_1f7156cb5da0fb8');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_A4DE6C675E237E06  RENAME TO uniq_2877fd485e237e06');
		$this->addSql('ALTER INDEX IF EXISTS IDX_A4DE6C67FFF2BAD2  RENAME TO idx_e5562a2afff2bad2');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_E114A52CB4917A01  RENAME TO uniq_e114a52c9ebce684');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_3E11D512ECEC1505  RENAME TO uniq_e889af96ef1ca3b8');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_BA98F5AC322D4901  RENAME TO uniq_ba98f5ac213c6f00');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_8CF12E7F2D4EB5D5  RENAME TO uniq_8cf12e7ff8512177');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_FFD094438493DD29  RENAME TO uniq_ffd0944387636b9c');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_A7E7925CCE9DAC40  RENAME TO uniq_b7e7621c6c03fa41');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_A7E7925C3E71406  RENAME TO uniq_a7e7613c5c03fa61');
		$this->addSql('ALTER INDEX IF EXISTS IDX_57B51F193E71406  RENAME TO idx_e863d8b33c08fa24');
		$this->addSql('ALTER INDEX IF EXISTS IDX_A65CA9F0CE9DAC40  RENAME TO idx_e773d8b53c08fa24');
		$this->addSql('ALTER INDEX IF EXISTS IDX_B3E3441EA76ED396  RENAME TO idx_5f37a13ba76ed395');
		$this->addSql('ALTER INDEX IF EXISTS IDX_2B9B5FC19395C3F4  RENAME TO idx_418857d79395c3f3');
		$this->addSql('ALTER INDEX IF EXISTS IDX_2B9B5FC160984F52  RENAME TO idx_418857d760984f51');
		$this->addSql('ALTER INDEX IF EXISTS IDX_2B9B5FC1E304DE5  RENAME TO idx_418857d7dda81d53');
		$this->addSql('ALTER INDEX IF EXISTS IDX_122C91B5727ACA71  RENAME TO idx_9f8d3b6a727aca70');
		$this->addSql('ALTER INDEX IF EXISTS IDX_122C91B5FF562E62  RENAME TO idx_9f8d3b6a8cba9bfc');
		$this->addSql('ALTER INDEX IF EXISTS IDX_603EB0D1CAE716F  RENAME TO idx_83ea7c58a4f1f82');
		$this->addSql('ALTER INDEX IF EXISTS IDX_603EB0DFF562E62  RENAME TO idx_83ea7c588cba9bfc');
		$this->addSql('ALTER INDEX IF EXISTS IDX_603EB0DBF396751  RENAME TO idx_83ea7c58bf396750');
		$this->addSql('ALTER INDEX IF EXISTS IDX_7BEBB85A1CAE717D  RENAME TO idx_606ff4b6a4f1f82');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_A7E7925CCE9DAC39  RENAME TO uniq_a7e7925cce9dac40');
		$this->addSql('ALTER INDEX IF EXISTS UNIQ_A7E7925C3E71405  RENAME TO uniq_a7e7925c3e71406');
		$this->addSql('ALTER INDEX IF EXISTS IDX_57B51F193E71405  RENAME TO idx_57b51f193e71406');
		$this->addSql('ALTER INDEX IF EXISTS IDX_A65CA9F0CE9DAC39  RENAME TO idx_a65ca9f0ce9dac40');
		$this->addSql('ALTER INDEX IF EXISTS IDX_B3E3441EA76ED395  RENAME TO idx_b3e3441ea76ed396');
		$this->addSql('ALTER INDEX IF EXISTS IDX_2B9B5FC19395C3F3  RENAME TO idx_2b9b5fc19395c3f4');
		$this->addSql('ALTER INDEX IF EXISTS IDX_2B9B5FC160984F51  RENAME TO idx_2b9b5fc160984f52');
		$this->addSql('ALTER INDEX IF EXISTS IDX_2B9B5FC1E304DE4  RENAME TO idx_2b9b5fc1e304de5');
		$this->addSql('ALTER INDEX IF EXISTS IDX_122C91B5727ACA70  RENAME TO idx_122c91b5727aca71');
		$this->addSql('ALTER INDEX IF EXISTS IDX_122C91B5FF562E61  RENAME TO idx_122c91b5ff562e62');
		$this->addSql('ALTER INDEX IF EXISTS IDX_603EB0D1CAE716D  RENAME TO idx_603eb0d1cae716f');
		$this->addSql('ALTER INDEX IF EXISTS IDX_603EB0DFF562E61  RENAME TO idx_603eb0dff562e62');
		$this->addSql('ALTER INDEX IF EXISTS IDX_603EB0DBF396750  RENAME TO idx_603eb0dbf396751');
		$this->addSql('ALTER INDEX IF EXISTS IDX_7BEBB85A1CAE716D  RENAME TO idx_7bebb85a1cae717d');

		$this->addSql('DROP  INDEX IF EXISTS idx_533d33c15a2f1036');
		$this->addSql('DROP  INDEX IF EXISTS uniq_3e11d512ecec1504');
		$this->addSql('DROP  INDEX IF EXISTS uniq_ba98f5ac322d4900');
		$this->addSql('DROP  INDEX IF EXISTS uniq_8cf12e7f2d4eb5d4');
		$this->addSql('DROP  INDEX IF EXISTS uniq_ffd094438493dd28');
	}
}
