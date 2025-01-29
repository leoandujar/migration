<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804145900 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE quote ADD cost_center VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD nuid VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD billing_contact VARCHAR(1000) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE project DROP cost_center');
		$this->addSql('ALTER TABLE project DROP nuid');
		$this->addSql('ALTER TABLE project DROP billing_contact');
	}
}
