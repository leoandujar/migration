<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728193007 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE project_template_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE project_template (project_template_id BIGINT NOT NULL, contact_person_id BIGINT NOT NULL, name VARCHAR(50) NOT NULL, type INT NOT NULL, data JSON DEFAULT \'{}\' NOT NULL, PRIMARY KEY(project_template_id))');
		$this->addSql('CREATE INDEX IDX_AA2E9458CD15F843 ON project_template (project_template_id)');
		$this->addSql('CREATE INDEX IDX_AA2E94584F8A983C ON project_template (contact_person_id)');
		$this->addSql('CREATE INDEX IDX_AA2E94585E237E06 ON project_template (name)');
		$this->addSql('CREATE INDEX IDX_AA2E94588CDE5729 ON project_template (type)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_AA2E94585E237E068CDE57294F8A983C ON project_template (name, type, contact_person_id)');
		$this->addSql('ALTER TABLE project_template ADD CONSTRAINT FK_AA2E94584F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE project_template_id_sequence CASCADE');
		$this->addSql('DROP TABLE project_template');
	}
}
