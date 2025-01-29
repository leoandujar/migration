<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201222190355 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_contact_person ALTER subscriber_to_newsletter SET DEFAULT \'false\'');
		$this->addSql('ALTER TABLE hs_contact_person RENAME COLUMN hs_contact_person TO hs_contact_person_id');
		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN hs_lead_id TO hs_deal_id');
		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN pipeline TO pipeline_id');
		$this->addSql('DROP INDEX uniq_13e7151462b6a46e');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_contact_person ALTER subscriber_to_newsletter DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_contact_person RENAME COLUMN hs_contact_person_id TO hs_contact_person');
		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN hs_deal_id TO hs_lead_id');
		$this->addSql('ALTER TABLE hs_deal RENAME COLUMN pipeline_id TO pipeline');
		$this->addSql('CREATE UNIQUE INDEX uniq_13e7151462b6a46e ON xtrf_user (hs_owner_id)');
	}
}
