<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819161440 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_customer ADD hs_parent_customer_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD division VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD close_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD recent_deal_close_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD current_account_com_status VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD days_to_close VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD lead_status VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD last_engagement_date VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD last_contacted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD referral_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD referral_type VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD referred_by VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD responsible_for_referral VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CDE5C3ED04 FOREIGN KEY (hs_parent_customer_id) REFERENCES hs_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_D51E27CDE5C3ED04 ON hs_customer (hs_parent_customer_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_customer DROP CONSTRAINT FK_D51E27CDE5C3ED04');
		$this->addSql('DROP INDEX IDX_D51E27CDE5C3ED04');
		$this->addSql('ALTER TABLE hs_customer DROP hs_parent_customer_id');
		$this->addSql('ALTER TABLE hs_customer DROP division');
		$this->addSql('ALTER TABLE hs_customer DROP close_date');
		$this->addSql('ALTER TABLE hs_customer DROP recent_deal_close_date');
		$this->addSql('ALTER TABLE hs_customer DROP current_account_com_status');
		$this->addSql('ALTER TABLE hs_customer DROP days_to_close');
		$this->addSql('ALTER TABLE hs_customer DROP lead_status');
		$this->addSql('ALTER TABLE hs_customer DROP last_engagement_date');
		$this->addSql('ALTER TABLE hs_customer DROP last_contacted_date');
		$this->addSql('ALTER TABLE hs_customer DROP referral_date');
		$this->addSql('ALTER TABLE hs_customer DROP referral_type');
		$this->addSql('ALTER TABLE hs_customer DROP referred_by');
		$this->addSql('ALTER TABLE hs_customer DROP responsible_for_referral');
	}
}
