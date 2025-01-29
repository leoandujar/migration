<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322213619 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE activity ALTER auto_correct_file_policy SET DEFAULT true');
		$this->addSql('ALTER TABLE analytics_project_step ADD CONSTRAINT FK_A4B70CF6CE6064C2 FOREIGN KEY (xtrf_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE charge_definition ADD PRIMARY KEY (charge_definition_id)');
		$this->addSql('ALTER TABLE contact_person DROP role');
		$this->addSql('ALTER TABLE custom_field_configuration ALTER preferences SET DEFAULT \'\'\'READ_WRITE\'\'::character varying\'');
		$this->addSql('ALTER TABLE customer_person ALTER first_project_date_auto SET NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER first_quote_date_auto SET NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_projects SET NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_quotes SET NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER dqa_report_id SET NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER dqa_category_id SET NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER id SET NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER minor SET NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER major SET NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER critical SET NOT NULL');
		$this->addSql('ALTER INDEX idx_d032f113a4f1f82 RENAME TO IDX_83EA7C58A4F1F82');
		$this->addSql('ALTER INDEX idx_d032f1138cba9bfc RENAME TO IDX_83EA7C588CBA9BFC');
		$this->addSql('ALTER INDEX idx_d032f113bf396750 RENAME TO IDX_83EA7C58BF396750');
		$this->addSql('ALTER INDEX uniq_606ff4b6ac74095a RENAME TO UNIQ_606FF4B681C06096');
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT fk_e04992aaa76ed395');
		$this->addSql('ALTER TABLE project DROP deliver_source');
		$this->addSql('ALTER TABLE project_resource ADD PRIMARY KEY (project_resource_id)');
		$this->addSql('ALTER TABLE provider_invoice ALTER vat_calculation_rule SET DEFAULT \'\'\'SUM_ITEMS\'\'::character varying\'');
		$this->addSql('ALTER TABLE quote ALTER total_agreed SET DEFAULT 0');
		$this->addSql('ALTER TABLE quote ALTER total_cost TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE task_additional_contact_persons ADD PRIMARY KEY (task_id, person_id)');
		$this->addSql('ALTER TABLE tm_savings ALTER rounding_policy SET DEFAULT \'\'\'ROUND_LAST\'\'::text\'');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE contact_person ADD role VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE analytics_project_step DROP CONSTRAINT FK_A4B70CF6CE6064C2');
		$this->addSql('ALTER TABLE custom_field_configuration ALTER preferences SET DEFAULT \'READ_WRITE\'');
		$this->addSql('ALTER TABLE customer_person ALTER first_project_date_auto DROP NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER first_quote_date_auto DROP NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_projects DROP NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_quotes DROP NOT NULL');
		$this->addSql('ALTER INDEX uniq_606ff4b681c06096 RENAME TO uniq_606ff4b6ac74095a');
		$this->addSql('ALTER TABLE dqa_issue ALTER id DROP NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER dqa_report_id DROP NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER dqa_category_id DROP NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER minor DROP NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER major DROP NOT NULL');
		$this->addSql('ALTER TABLE dqa_issue ALTER critical DROP NOT NULL');
		$this->addSql('ALTER INDEX idx_83ea7c588cba9bfc RENAME TO idx_d032f1138cba9bfc');
		$this->addSql('ALTER INDEX idx_83ea7c58bf396750 RENAME TO idx_d032f113bf396750');
		$this->addSql('ALTER INDEX idx_83ea7c58a4f1f82 RENAME TO idx_d032f113a4f1f82');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT fk_e04992aaa76ed395 FOREIGN KEY (internal_user_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE project ADD deliver_source BOOLEAN DEFAULT \'false\'');
		$this->addSql('ALTER TABLE provider_invoice ALTER vat_calculation_rule SET DEFAULT \'SUM_ITEMS\'');
		$this->addSql('ALTER TABLE quote ALTER total_agreed DROP DEFAULT');
		$this->addSql('ALTER TABLE quote ALTER total_cost TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE tm_savings ALTER rounding_policy SET DEFAULT \'ROUND_LAST\'');
		$this->addSql('ALTER TABLE activity ALTER auto_correct_file_policy DROP DEFAULT');
	}
}
