<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410182816 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944589CA0172C FOREIGN KEY (related_project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_D22944589CA0172C ON feedback (related_project_id)');
		$this->addSql('ALTER TABLE contact_person DROP username');
		$this->addSql('ALTER TABLE contact_person ALTER role TYPE VARCHAR(32)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D22944589CA0172C');
		$this->addSql('DROP INDEX IDX_D22944589CA0172C');
		$this->addSql('ALTER TABLE contact_person ADD username VARCHAR(60) DEFAULT NULL');
		$this->addSql('ALTER TABLE contact_person ALTER role TYPE VARCHAR(255)');
	}
}
