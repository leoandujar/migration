<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211118230438 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE customer ADD roles JSON DEFAULT NULL');

		$this->addSql('ALTER TABLE permission ADD cp_customer_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA2D0E18D3 FOREIGN KEY (cp_customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_E04992AA2D0E18D3 ON permission (cp_customer_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE customer DROP roles');

		$this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AA2D0E18D3');
		$this->addSql('DROP INDEX IDX_E04992AA2D0E18D3');
		$this->addSql('ALTER TABLE permission DROP cp_customer_id');
	}
}
