<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507232304 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE quality_report ADD minor_multiplier INT DEFAULT 1 NOT NULL');
		$this->addSql('ALTER TABLE quality_report ADD major_multiplier INT DEFAULT 5 NOT NULL');
		$this->addSql('ALTER TABLE quality_report ADD critical_multiplier INT DEFAULT 9 NOT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE quality_report DROP minor_multiplier');
		$this->addSql('ALTER TABLE quality_report DROP major_multiplier');
		$this->addSql('ALTER TABLE quality_report DROP critical_multiplier');
	}
}
