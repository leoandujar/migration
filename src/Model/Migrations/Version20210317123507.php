<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210317123507 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE customer ADD new_business VARCHAR(255) DEFAULT NULL');
		$this->addSql('CREATE SEQUENCE hs_customer_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE hs_contact_person_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE hs_deal_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE hs_marketing_email_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE customer DROP new_business');
		$this->addSql('DROP SEQUENCE hs_customer_id_sequence');
		$this->addSql('DROP SEQUENCE hs_contact_person_id_sequence');
		$this->addSql('DROP SEQUENCE hs_deal_id_sequence');
		$this->addSql('DROP SEQUENCE hs_marketing_email_id_sequence');
	}
}
