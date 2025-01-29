<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200426185934 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE customer_person ADD first_project_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD first_project_date_auto BOOLEAN DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD first_quote_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD first_quote_date_auto BOOLEAN DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD last_project_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD last_quote_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD number_of_projects INT DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_person ADD number_of_quotes INT DEFAULT NULL');
		$this->addSql('ALTER TABLE customer_invoice ADD sent_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD confirmation_sent_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD created_by_user_signed_in_as_partner_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE task_finance ADD confirmation_sent_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD created_by_user_signed_in_as_partner_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD sent_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD purchase_order_sent_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE activity ADD auto_correct_file_policy BOOLEAN DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE activity DROP purchase_order_sent_date');
		$this->addSql('ALTER TABLE activity DROP auto_correct_file_policy');
		$this->addSql('ALTER TABLE quote DROP created_by_user_signed_in_as_partner_id');
		$this->addSql('ALTER TABLE customer_invoice DROP sent_date');
		$this->addSql('ALTER TABLE customer_person DROP first_project_date');
		$this->addSql('ALTER TABLE customer_person DROP first_project_date_auto');
		$this->addSql('ALTER TABLE customer_person DROP first_quote_date');
		$this->addSql('ALTER TABLE customer_person DROP first_quote_date_auto');
		$this->addSql('ALTER TABLE customer_person DROP last_project_date');
		$this->addSql('ALTER TABLE customer_person DROP last_quote_date');
		$this->addSql('ALTER TABLE customer_person DROP number_of_projects');
		$this->addSql('ALTER TABLE customer_person DROP number_of_quotes');
		$this->addSql('ALTER TABLE project DROP confirmation_sent_date');
		$this->addSql('ALTER TABLE project DROP created_by_user_signed_in_as_partner_id');
		$this->addSql('ALTER TABLE task_finance DROP confirmation_sent_date');
		$this->addSql('ALTER TABLE quote DROP sent_date');
	}
}
