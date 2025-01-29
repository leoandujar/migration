<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201013203234 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ADD total_activities INT DEFAULT NULL');
		$this->addSql('ALTER TABLE task ADD progress_activities INT DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD total_activities INT DEFAULT NULL');
		$this->addSql('ALTER TABLE project ADD progress_activities INT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE project DROP total_activities');
		$this->addSql('ALTER TABLE project DROP progress_activities');
		$this->addSql('ALTER TABLE task DROP total_activities');
		$this->addSql('ALTER TABLE task DROP progress_activities');
	}
}
