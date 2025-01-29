<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201001231605 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE project_template_id_sequence CASCADE');
		$this->addSql('CREATE SEQUENCE cp_template_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cp_template (cp_template_id BIGINT NOT NULL,contact_person_id BIGINT NOT NULL,name VARCHAR(50) NOT NULL,type INT NOT NULL,target_entity INT DEFAULT 1 NOT NULL,data JSON DEFAULT \'{}\' NOT NULL,PRIMARY KEY(cp_template_id))');
		$this->addSql('CREATE INDEX IDX_B518A00BB41D498 ON cp_template (cp_template_id)');
		$this->addSql('CREATE INDEX IDX_B518A004F8A983C ON cp_template (contact_person_id)');
		$this->addSql('CREATE INDEX IDX_B518A005E237E06 ON cp_template (name)');
		$this->addSql('CREATE INDEX IDX_B518A008CDE5729 ON cp_template (type)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_B518A005E237E068CDE57294F8A983C ON cp_template (name, type, contact_person_id)');
		$this->addSql('ALTER TABLE cp_template ADD CONSTRAINT FK_B518A004F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('DROP TABLE project_template');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE cp_template_id_sequence CASCADE');
		$this->addSql('CREATE SEQUENCE project_template_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE project_template (project_template_id BIGINT NOT NULL, contact_person_id BIGINT NOT NULL, name VARCHAR(50) NOT NULL, type INT NOT NULL, data JSON DEFAULT \'{}\' NOT NULL, PRIMARY KEY(project_template_id))');
		$this->addSql('CREATE INDEX idx_aa2e94588cde5729 ON project_template (type)');
		$this->addSql('CREATE INDEX idx_aa2e94585e237e06 ON project_template (name)');
		$this->addSql('CREATE INDEX idx_aa2e9458cd15f843 ON project_template (project_template_id)');
		$this->addSql('CREATE INDEX idx_aa2e94584f8a983c ON project_template (contact_person_id)');
		$this->addSql('CREATE UNIQUE INDEX uniq_aa2e94585e237e068cde57294f8a983c ON project_template (name, type, contact_person_id)');
		$this->addSql('ALTER TABLE project_template ADD CONSTRAINT fk_aa2e94584f8a983c FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('DROP TABLE cp_template');
	}
}
