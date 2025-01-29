<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220215131319 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE project ADD source VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD source VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE project DROP error');
		$this->addSql('ALTER TABLE project DROP template');
		$this->addSql('ALTER TABLE project DROP billing_contact_legacy');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE project ADD error VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD template TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD billing_contact_legacy TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE project DROP source');
		$this->addSql('ALTER TABLE quote DROP source');
	}
}
