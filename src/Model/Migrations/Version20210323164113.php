<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210323164113 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE authentication_history_id_sequence CASCADE');
		$this->addSql('CREATE INDEX IDX_85115C1081C06096 ON analytics_project (activity_id)');
		$this->addSql('ALTER TABLE authentication_history ADD authentication_history_id VARCHAR(36) NOT NULL default uuid_generate_v4()');
		$this->addSql('ALTER TABLE authentication_history ADD PRIMARY KEY (authentication_history_id, client_ip, is_successful, login, login_date)');
		$this->addSql('ALTER TABLE analytics_project ADD CONSTRAINT FK_85115C108DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE analytics_project ADD CONSTRAINT FK_85115C1081C06096 FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_A7E7925C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_person ALTER first_project_date_auto SET DEFAULT true');
		$this->addSql('ALTER TABLE customer_person ALTER first_quote_date_auto SET DEFAULT true');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_projects SET DEFAULT 0');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_quotes SET DEFAULT 0');
		$this->addSql('ALTER TABLE dqa_report ADD CONSTRAINT FK_606FF4B681C06096 FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE project ALTER total_activities SET DEFAULT 0');
		$this->addSql('ALTER TABLE project ALTER progress_activities SET DEFAULT 0');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item ADD CONSTRAINT FK_826F5A05D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE task ALTER wa_translator_name TYPE TEXT');
		$this->addSql('ALTER TABLE task ALTER wa_translator_name DROP DEFAULT');
		$this->addSql('ALTER TABLE task ALTER wa_translator_name TYPE TEXT');
		$this->addSql('ALTER TABLE task ALTER wa_reviewer_name TYPE TEXT');
		$this->addSql('ALTER TABLE task ALTER wa_reviewer_name DROP DEFAULT');
		$this->addSql('ALTER TABLE task ALTER wa_reviewer_name TYPE TEXT');
		$this->addSql('ALTER TABLE task ALTER total_activities SET DEFAULT 0');
		$this->addSql('ALTER TABLE task ALTER progress_activities SET DEFAULT 0');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('DROP INDEX IDX_85115C1081C06096');
		$this->addSql('ALTER TABLE authentication_history DROP authentication_history_id');
		$this->addSql('CREATE SEQUENCE authentication_history_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('ALTER TABLE analytics_project DROP CONSTRAINT FK_85115C108DB60186');
		$this->addSql('ALTER TABLE analytics_project DROP CONSTRAINT FK_85115C1081C06096');
		$this->addSql('ALTER TABLE contact_person ADD role VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE dqa_report DROP CONSTRAINT FK_606FF4B681C06096');
		$this->addSql('ALTER TABLE qbo_customer_invoice_item DROP CONSTRAINT FK_826F5A05D440F57F');
		$this->addSql('ALTER TABLE task ALTER wa_translator_name TYPE VARCHAR(1000)');
		$this->addSql('ALTER TABLE task ALTER wa_translator_name SET DEFAULT \'NULL::character varying\'');
		$this->addSql('ALTER TABLE task ALTER wa_reviewer_name TYPE VARCHAR(1000)');
		$this->addSql('ALTER TABLE task ALTER wa_reviewer_name SET DEFAULT \'NULL::character varying\'');
		$this->addSql('ALTER TABLE task ALTER total_activities DROP DEFAULT');
		$this->addSql('ALTER TABLE task ALTER progress_activities DROP DEFAULT');
		$this->addSql('ALTER TABLE project ALTER total_activities DROP DEFAULT');
		$this->addSql('ALTER TABLE project ALTER progress_activities DROP DEFAULT');
		$this->addSql('ALTER TABLE customer_person ALTER first_project_date_auto DROP DEFAULT');
		$this->addSql('ALTER TABLE customer_person ALTER first_project_date_auto DROP NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER first_quote_date_auto DROP DEFAULT');
		$this->addSql('ALTER TABLE customer_person ALTER first_quote_date_auto DROP NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_projects DROP DEFAULT');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_projects DROP NOT NULL');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_quotes DROP DEFAULT');
		$this->addSql('ALTER TABLE customer_person ALTER number_of_quotes DROP NOT NULL');
		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT FK_A7E7925C9395C3F3');
	}
}
