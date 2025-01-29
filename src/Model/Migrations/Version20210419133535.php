<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419133535 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE branch ADD home_portal_color_properties JSONB DEFAULT NULL');
		$this->addSql('ALTER TABLE branch ADD vendor_portal_color_properties JSONB DEFAULT NULL');
		$this->addSql('ALTER TABLE branch ADD client_portal_color_properties JSONB DEFAULT NULL');
		$this->addSql('DROP INDEX idx_61134782a8eedb5f');
		$this->addSql('ALTER TABLE internal_user ADD hs_owner_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE internal_user RENAME COLUMN internal_user_id_sequence TO internal_user_id');
		$this->addSql('CREATE INDEX IDX_61134782BF7692A3 ON internal_user (internal_user_id)');
		$this->addSql('ALTER TABLE contact_person ALTER role TYPE VARCHAR(32)');
		$this->addSql('ALTER TABLE hs_contact_person DROP CONSTRAINT FK_55056597E3C61F9');
		$this->addSql('ALTER TABLE hs_contact_person ADD CONSTRAINT FK_55056597E3C61F9 FOREIGN KEY (owner_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_customer DROP CONSTRAINT FK_D51E27CD7E3C61F9');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_deal DROP CONSTRAINT FK_D07AE4837E3C61F9');
		$this->addSql('ALTER TABLE hs_deal ADD CONSTRAINT FK_D07AE4837E3C61F9 FOREIGN KEY (owner_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AABF7692A3');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AABF7692A3 FOREIGN KEY (internal_user_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE xtrf_user DROP hs_owner_id');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('ALTER TABLE branch DROP home_portal_color_properties');
		$this->addSql('ALTER TABLE branch DROP vendor_portal_color_properties');
		$this->addSql('ALTER TABLE branch DROP client_portal_color_properties');
		$this->addSql('ALTER TABLE contact_person ALTER role TYPE VARCHAR(60)');
		$this->addSql('DROP INDEX IDX_61134782BF7692A3');
		$this->addSql('DROP INDEX internal_user_pkey');
		$this->addSql('ALTER TABLE internal_user DROP hs_owner_id');
		$this->addSql('ALTER TABLE internal_user RENAME COLUMN internal_user_id TO internal_user_id_sequence');
		$this->addSql('CREATE INDEX idx_61134782a8eedb5f ON internal_user (internal_user_id_sequence)');
		$this->addSql('ALTER TABLE internal_user ADD PRIMARY KEY (internal_user_id_sequence)');
		$this->addSql('ALTER TABLE hs_customer DROP CONSTRAINT fk_d51e27cd7e3c61f9');
		$this->addSql('ALTER TABLE hs_customer ADD CONSTRAINT fk_d51e27cd7e3c61f9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_deal DROP CONSTRAINT fk_d07ae4837e3c61f9');
		$this->addSql('ALTER TABLE hs_deal ADD CONSTRAINT fk_d07ae4837e3c61f9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE hs_contact_person DROP CONSTRAINT fk_55056597e3c61f9');
		$this->addSql('ALTER TABLE hs_contact_person ADD CONSTRAINT fk_55056597e3c61f9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT fk_e04992aabf7692a3');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT fk_e04992aabf7692a3 FOREIGN KEY (internal_user_id) REFERENCES internal_user (internal_user_id_sequence) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE xtrf_user ADD hs_owner_id VARCHAR(255) DEFAULT NULL');
	}
}
