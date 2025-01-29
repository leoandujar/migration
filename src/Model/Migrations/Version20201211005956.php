<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201211005956 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE hs_contact_person (id BIGINT NOT NULL, contact_person_id BIGINT DEFAULT NULL, owner_id BIGINT DEFAULT NULL, hs_contact_person BIGINT NOT NULL, hs_customer_id BIGINT NOT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_activity_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, first_deal_created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, first_conversion_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, became_customer_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, became_lead_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, became_marketing_lead_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, became_sales_lead_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, became_subscriber_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, became_opportunity_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, buying_role VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, industry VARCHAR(255) DEFAULT NULL, job_title VARCHAR(255) DEFAULT NULL, lead_source_event VARCHAR(255) DEFAULT NULL, lifecicle_stage VARCHAR(255) DEFAULT NULL, sales_activities INT DEFAULT NULL, persona VARCHAR(255) DEFAULT NULL, last_sales_email_last_opened_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_sales_email_last_replied_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_sales_email_last_clicked_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, subscriber_to_newsletter BOOLEAN DEFAULT NULL, facebook_clicks INT DEFAULT NULL, twitter_clicks INT DEFAULT NULL, linkedin_clicks INT DEFAULT NULL, email_first_click_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email_first_open_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email_first_send_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email_last_click_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email_last_open_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email_last_send_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email_clicks INT DEFAULT NULL, email_delivered INT DEFAULT NULL, email_opened INT DEFAULT NULL, email_sends_since_last_engagement INT DEFAULT NULL, fist_referrer_site VARCHAR(255) DEFAULT NULL, first_url VARCHAR(255) DEFAULT NULL, visits INT DEFAULT NULL, source VARCHAR(255) DEFAULT NULL, first_conversion VARCHAR(255) DEFAULT NULL, lead_source VARCHAR(255) DEFAULT NULL, referrer_first_name VARCHAR(255) DEFAULT NULL, referrer_last_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_55056594F8A983C ON hs_contact_person (contact_person_id)');
		$this->addSql('CREATE INDEX IDX_55056597E3C61F9 ON hs_contact_person (owner_id)');
		$this->addSql('CREATE TABLE hs_customer (id BIGINT NOT NULL, customer_id BIGINT DEFAULT NULL, owner_id BIGINT DEFAULT NULL, hs_customer_id BIGINT NOT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_activity_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, first_deal_created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, first_conversion_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, lifecicle_stage VARCHAR(255) DEFAULT NULL, likelihood_to_close NUMERIC(16, 2) DEFAULT NULL, name VARCHAR(255) NOT NULL, open_deals INT DEFAULT NULL, contacted INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, total_deal_value NUMERIC(16, 2) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, visits INT DEFAULT NULL, source_data VARCHAR(255) DEFAULT NULL, source_type VARCHAR(255) DEFAULT NULL, sale_type VARCHAR(255) DEFAULT NULL, first_visit TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_visit TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, acquisition_type VARCHAR(255) DEFAULT NULL, form_submissions INT DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_D51E27CD9395C3F3 ON hs_customer (customer_id)');
		$this->addSql('CREATE INDEX IDX_D51E27CD7E3C61F9 ON hs_customer (owner_id)');
		$this->addSql('CREATE TABLE hs_deal (id BIGINT NOT NULL, owner_id BIGINT DEFAULT NULL, hs_lead_id BIGINT NOT NULL, amount NUMERIC(16, 2) DEFAULT NULL, closed_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) NOT NULL, stage VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, estimated_rfp_amount NUMERIC(16, 2) DEFAULT NULL, industry VARCHAR(255) DEFAULT NULL, last_activity_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sales_activities INT DEFAULT NULL, pipeline VARCHAR(255) DEFAULT NULL, reason_deal_lost VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_D07AE4837E3C61F9 ON hs_deal (owner_id)');
		$this->addSql('CREATE TABLE hs_marketing_email (id BIGINT NOT NULL, hs_marketing_email_id BIGINT NOT NULL, name VARCHAR(255) NOT NULL, successful_delivery INT DEFAULT NULL, opt_in_out NUMERIC(16, 2) DEFAULT NULL, archived BOOLEAN NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, publish_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, sent_count INT DEFAULT NULL, open_count INT DEFAULT NULL, delivered_count INT DEFAULT NULL, bounce_count INT DEFAULT NULL, unsubscriber_count INT DEFAULT NULL, click_count INT DEFAULT NULL, open_ratio NUMERIC(16, 2) DEFAULT NULL, delivered_ratio NUMERIC(16, 2) DEFAULT NULL, bounce_ratio NUMERIC(16, 2) DEFAULT NULL, unsubscribed_ratio NUMERIC(16, 2) DEFAULT NULL, click_ratio NUMERIC(16, 2) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('ALTER TABLE hs_contact_person ADD CONSTRAINT FK_55056594F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_contact_person ADD CONSTRAINT FK_55056597E3C61F9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CD9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_deal ADD CONSTRAINT FK_D07AE4837E3C61F9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE xtrf_user ADD hs_owner_id VARCHAR(255) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE hs_contact_person');
		$this->addSql('DROP TABLE hs_customer');
		$this->addSql('DROP TABLE hs_deal');
		$this->addSql('DROP TABLE hs_marketing_email');
		$this->addSql('ALTER TABLE xtrf_user DROP hs_owner_id');
	}
}
