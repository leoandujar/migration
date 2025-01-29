<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221021175310 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE action RENAME TO av_action');
		$this->addSql('ALTER TABLE analytics_project RENAME TO xtm_analytics_project');
		$this->addSql('ALTER TABLE analytics_project_step RENAME TO xtm_analytics_project_step');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts RENAME TO av_reports_templates_charts');
		$this->addSql('ALTER TABLE chart_group_assign RENAME TO av_report_chart_group');
		$this->addSql('ALTER TABLE internal_user RENAME TO av_user');
		$this->addSql('ALTER TABLE lqa_issue RENAME TO xtm_lqa_issue');
		$this->addSql('ALTER TABLE lqa_issue_type RENAME TO xtm_lqa_issue_type');
		$this->addSql('ALTER TABLE lqa_issue_type_mapping RENAME TO xtm_lqa_issue_type_mapping');
		$this->addSql('ALTER TABLE permission RENAME TO av_permission');
		$this->addSql('ALTER TABLE quality_answer RENAME TO ap_quality_answer');
		$this->addSql('ALTER TABLE quality_category RENAME TO ap_quality_category');
		$this->addSql('ALTER TABLE quality_issue RENAME TO ap_quality_issue');
		$this->addSql('ALTER TABLE quality_report RENAME TO ap_quality_report');
		$this->addSql('ALTER TABLE role RENAME TO av_role');
		$this->addSql('ALTER TABLE wf_history RENAME TO av_workflow_history');
		$this->addSql('ALTER TABLE wf_params RENAME TO av_workflow_params');
		$this->addSql('ALTER TABLE wf_workflow RENAME TO av_workflow');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_action RENAME TO action');
		$this->addSql('ALTER TABLE xtm_analytics_project RENAME TO analytics_project');
		$this->addSql('ALTER TABLE xtm_analytics_project_step RENAME TO analytics_project_step');
		$this->addSql('ALTER TABLE av_reports_templates_charts RENAME TO av_piv_reports_templates_charts');
		$this->addSql('ALTER TABLE av_report_chart_group RENAME TO chart_group_assign');
		$this->addSql('ALTER TABLE av_user RENAME TO internal_user');
		$this->addSql('ALTER TABLE xtm_lqa_issue RENAME TO lqa_issue');
		$this->addSql('ALTER TABLE xtm_lqa_issue_type RENAME TO lqa_issue_type');
		$this->addSql('ALTER TABLE xtm_lqa_issue_type_mapping RENAME TO lqa_issue_type_mapping');
		$this->addSql('ALTER TABLE av_permission RENAME TO permission');
		$this->addSql('ALTER TABLE ap_quality_answer RENAME TO quality_answer');
		$this->addSql('ALTER TABLE ap_quality_category RENAME TO quality_category');
		$this->addSql('ALTER TABLE ap_quality_issue RENAME TO quality_issue');
		$this->addSql('ALTER TABLE ap_quality_report RENAME TO quality_report');
		$this->addSql('ALTER TABLE av_role RENAME TO role');
		$this->addSql('ALTER TABLE av_workflow_history RENAME TO wf_history');
		$this->addSql('ALTER TABLE av_workflow_params RENAME TO wf_params');
		$this->addSql('ALTER TABLE av_workflow RENAME TO wf_workflow');
	}
}
