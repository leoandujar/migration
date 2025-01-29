<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819150802 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_contact_person ADD division VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD lifecyclestage_other_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD child_company VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD company VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD last_contacted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD last_engagement_date VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD lead_status VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD mql_score VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD num_sequences_enrolled VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD num_times_contacted VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD reference VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD willing_to_be_a_reference VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ADD num_form_submissions VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_deal DROP contract_term_ending_date');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_deal ALTER contract_term_ending_date TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE hs_deal ALTER contract_term_ending_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_contact_person DROP division');
		$this->addSql('ALTER TABLE hs_contact_person DROP lifecyclestage_other_date');
		$this->addSql('ALTER TABLE hs_contact_person DROP child_company');
		$this->addSql('ALTER TABLE hs_contact_person DROP company');
		$this->addSql('ALTER TABLE hs_contact_person DROP last_contacted_date');
		$this->addSql('ALTER TABLE hs_contact_person DROP last_engagement_date');
		$this->addSql('ALTER TABLE hs_contact_person DROP lead_status');
		$this->addSql('ALTER TABLE hs_contact_person DROP mql_score');
		$this->addSql('ALTER TABLE hs_contact_person DROP num_sequences_enrolled');
		$this->addSql('ALTER TABLE hs_contact_person DROP num_times_contacted');
		$this->addSql('ALTER TABLE hs_contact_person DROP reference');
		$this->addSql('ALTER TABLE hs_contact_person DROP willing_to_be_a_reference');
		$this->addSql('ALTER TABLE hs_contact_person DROP num_form_submissions');
	}
}
