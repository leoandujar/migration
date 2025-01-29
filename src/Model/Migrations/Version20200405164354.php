<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405164354 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ALTER total_agreed SET DEFAULT 0');
		$this->addSql('ALTER TABLE task ALTER rentability TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE task ALTER time_based_cost TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE project ALTER total_agreed TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE project ALTER time_based_cost TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE project ALTER tm_savings TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE project ALTER rentability TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE quote ALTER time_based_cost TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE quote ALTER tm_savings TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE quote ALTER total_agreed TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE quote ALTER rentability TYPE NUMERIC(19, 6)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ALTER total_agreed DROP DEFAULT');
		$this->addSql('ALTER TABLE task ALTER rentability TYPE NUMERIC(16, 2)');
		$this->addSql('ALTER TABLE task ALTER time_based_cost TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE quote ALTER time_based_cost TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE quote ALTER tm_savings TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE quote ALTER total_agreed TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE quote ALTER rentability TYPE NUMERIC(16, 2)');
		$this->addSql('ALTER TABLE project ALTER total_agreed TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE project ALTER time_based_cost TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE project ALTER tm_savings TYPE NUMERIC(10, 2)');
		$this->addSql('ALTER TABLE project ALTER rentability TYPE NUMERIC(10, 2)');
	}
}
