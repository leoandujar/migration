<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108194012 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_customer ADD customer_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_customer ADD CONSTRAINT FK_E114A52C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_E114A52C9395C3F3 ON bl_customer (customer_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_customer DROP CONSTRAINT FK_E114A52C9395C3F3');
		$this->addSql('DROP INDEX IDX_E114A52C9395C3F3');
		$this->addSql('ALTER TABLE bl_customer DROP customer_id');
	}
}
