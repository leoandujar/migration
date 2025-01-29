<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200404224022 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task DROP total_cost');
		$this->addSql('ALTER TABLE task ALTER rentability TYPE NUMERIC(16, 2)');
		$this->addSql('ALTER TABLE project ALTER margin TYPE NUMERIC(19, 5)');
		$this->addSql('ALTER TABLE project ALTER margin DROP DEFAULT');
		$this->addSql('ALTER TABLE quote ALTER margin TYPE NUMERIC(19, 5)');
		$this->addSql('ALTER TABLE quote ALTER margin DROP DEFAULT');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ADD total_cost NUMERIC(16, 2) DEFAULT NULL');
		$this->addSql('ALTER TABLE task ALTER rentability TYPE NUMERIC(16, 5)');
		$this->addSql('ALTER TABLE quote ALTER margin TYPE INT');
		$this->addSql('ALTER TABLE quote ALTER margin DROP DEFAULT');
		$this->addSql('ALTER TABLE project ALTER margin TYPE INT');
		$this->addSql('ALTER TABLE project ALTER margin DROP DEFAULT');
	}
}
