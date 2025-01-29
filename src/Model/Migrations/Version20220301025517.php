<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220301025517 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE internal_user DROP CONSTRAINT fk_6113478292c1bed0');
		$this->addSql('DROP INDEX idx_6113478292c1bed0');
		$this->addSql('ALTER TABLE internal_user ADD cp_login_customers JSON DEFAULT NULL');
		$this->addSql('ALTER TABLE internal_user DROP cp_login_customer');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE internal_user ADD cp_login_customer BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE internal_user DROP cp_login_customers');
		$this->addSql('ALTER TABLE internal_user ADD CONSTRAINT fk_6113478292c1bed0 FOREIGN KEY (cp_login_customer) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_6113478292c1bed0 ON internal_user (cp_login_customer)');
	}
}
