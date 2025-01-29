<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419205015 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE hs_engagement_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE hs_engagement_assoc_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE hs_engagement (hs_engagement_id BIGINT NOT NULL, created_by BIGINT DEFAULT NULL, modified_by BIGINT DEFAULT NULL, owner_id BIGINT DEFAULT NULL, hs_id VARCHAR(255) NOT NULL, portal_id VARCHAR(70) DEFAULT NULL, active BOOLEAN DEFAULT \'false\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_updated TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(70) DEFAULT NULL, timestamp INT NOT NULL, attachments JSON DEFAULT NULL, metadata JSON DEFAULT NULL, PRIMARY KEY(hs_engagement_id))');
		$this->addSql('CREATE INDEX IDX_D8F35C60DE12AB56 ON hs_engagement (created_by)');
		$this->addSql('CREATE INDEX IDX_D8F35C6025F94802 ON hs_engagement (modified_by)');
		$this->addSql('CREATE INDEX IDX_D8F35C607E3C61F9 ON hs_engagement (owner_id)');
		$this->addSql('CREATE INDEX IDX_D8F35C601C2CBF36 ON hs_engagement (hs_engagement_id)');
		$this->addSql('CREATE TABLE hs_engagement_assoc (hs_engagement_assoc_id BIGINT NOT NULL, hs_engagement_id BIGINT NOT NULL, hs_contact_id BIGINT DEFAULT NULL, hs_company_id BIGINT DEFAULT NULL, hs_deal_id BIGINT DEFAULT NULL, owner_id BIGINT DEFAULT NULL, workflow_id BIGINT DEFAULT NULL, PRIMARY KEY(hs_engagement_assoc_id))');
		$this->addSql('CREATE INDEX IDX_13F52BDC1C2CBF36 ON hs_engagement_assoc (hs_engagement_id)');
		$this->addSql('CREATE INDEX IDX_13F52BDCE73D786B ON hs_engagement_assoc (hs_contact_id)');
		$this->addSql('CREATE INDEX IDX_13F52BDC970747F7 ON hs_engagement_assoc (hs_company_id)');
		$this->addSql('CREATE INDEX IDX_13F52BDCFF0CEF3 ON hs_engagement_assoc (hs_deal_id)');
		$this->addSql('CREATE INDEX IDX_13F52BDC7E3C61F9 ON hs_engagement_assoc (owner_id)');
		$this->addSql('CREATE INDEX IDX_13F52BDC2C7C2CBA ON hs_engagement_assoc (workflow_id)');
		$this->addSql('CREATE INDEX IDX_13F52BDC5975F28D ON hs_engagement_assoc (hs_engagement_assoc_id)');
		$this->addSql('ALTER TABLE hs_engagement ADD CONSTRAINT FK_D8F35C60DE12AB56 FOREIGN KEY (created_by) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement ADD CONSTRAINT FK_D8F35C6025F94802 FOREIGN KEY (modified_by) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement ADD CONSTRAINT FK_D8F35C607E3C61F9 FOREIGN KEY (owner_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT FK_13F52BDC1C2CBF36 FOREIGN KEY (hs_engagement_id) REFERENCES hs_engagement (hs_engagement_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT FK_13F52BDCE73D786B FOREIGN KEY (hs_contact_id) REFERENCES hs_contact_person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT FK_13F52BDC970747F7 FOREIGN KEY (hs_company_id) REFERENCES hs_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT FK_13F52BDCFF0CEF3 FOREIGN KEY (hs_deal_id) REFERENCES hs_deal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT FK_13F52BDC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_engagement_assoc ADD CONSTRAINT FK_13F52BDC2C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('ALTER TABLE hs_engagement_assoc DROP CONSTRAINT FK_13F52BDC1C2CBF36');
		$this->addSql('DROP SEQUENCE hs_engagement_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE hs_engagement_assoc_sequence CASCADE');
		$this->addSql('DROP TABLE hs_engagement');
		$this->addSql('DROP TABLE hs_engagement_assoc');
	}
}
