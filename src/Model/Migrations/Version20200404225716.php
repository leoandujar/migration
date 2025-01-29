<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404225716 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ALTER margin TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE project ALTER margin TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE quote ALTER margin TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE quote ADD rentability NUMERIC(16, 2) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE quote DROP rentability');
		$this->addSql('ALTER TABLE task ALTER margin TYPE NUMERIC(19, 5)');
		$this->addSql('ALTER TABLE quote ALTER margin TYPE NUMERIC(19, 5)');
		$this->addSql('ALTER TABLE project ALTER margin TYPE NUMERIC(19, 5)');
	}
}
