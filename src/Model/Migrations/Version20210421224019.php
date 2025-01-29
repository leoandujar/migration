<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421224019 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE ap_form_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE ap_form_submission_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE ap_form_template_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE ap_form (ap_form_id BIGINT NOT NULL, created_by BIGINT NOT NULL, approver BIGINT NOT NULL, ap_form_template_id BIGINT NOT NULL, category INT DEFAULT NULL, name VARCHAR(30) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pmk_template_id VARCHAR(30) NOT NULL, PRIMARY KEY(ap_form_id))');
		$this->addSql('CREATE INDEX IDX_7200EFA7DE12AB56 ON ap_form (created_by)');
		$this->addSql('CREATE INDEX IDX_7200EFA788836D4C ON ap_form (approver)');
		$this->addSql('CREATE INDEX IDX_7200EFA73D915CE3 ON ap_form (ap_form_template_id)');
		$this->addSql('CREATE INDEX IDX_7200EFA763906184 ON ap_form (ap_form_id)');
		$this->addSql('CREATE TABLE ap_form_submission (ap_form_submission_id BIGINT NOT NULL, ap_form_id BIGINT NOT NULL, submitted_by BIGINT NOT NULL, approved_by BIGINT NOT NULL, status VARCHAR(15) NOT NULL, submitted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, submitted_data JSON NOT NULL, PRIMARY KEY(ap_form_submission_id))');
		$this->addSql('CREATE INDEX IDX_4894C55163906184 ON ap_form_submission (ap_form_id)');
		$this->addSql('CREATE INDEX IDX_4894C551641EE842 ON ap_form_submission (submitted_by)');
		$this->addSql('CREATE INDEX IDX_4894C5514EA3CB3D ON ap_form_submission (approved_by)');
		$this->addSql('CREATE INDEX IDX_4894C5514AD0F5A9 ON ap_form_submission (ap_form_submission_id)');
		$this->addSql('CREATE TABLE ap_form_template (ap_form_template_id BIGINT NOT NULL, name VARCHAR(30) NOT NULL, type VARCHAR(30) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(ap_form_template_id))');
		$this->addSql('CREATE INDEX IDX_257F2EC13D915CE3 ON ap_form_template (ap_form_template_id)');
		$this->addSql('ALTER TABLE ap_form ADD CONSTRAINT FK_7200EFA7DE12AB56 FOREIGN KEY (created_by) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE ap_form ADD CONSTRAINT FK_7200EFA788836D4C FOREIGN KEY (approver) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE ap_form ADD CONSTRAINT FK_7200EFA73D915CE3 FOREIGN KEY (ap_form_template_id) REFERENCES ap_form_template (ap_form_template_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE ap_form_submission ADD CONSTRAINT FK_4894C55163906184 FOREIGN KEY (ap_form_id) REFERENCES ap_form (ap_form_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE ap_form_submission ADD CONSTRAINT FK_4894C551641EE842 FOREIGN KEY (submitted_by) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE ap_form_submission ADD CONSTRAINT FK_4894C5514EA3CB3D FOREIGN KEY (approved_by) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE xtrf_user DROP hs_owner_id');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('ALTER TABLE ap_form_submission DROP CONSTRAINT FK_4894C55163906184');
		$this->addSql('ALTER TABLE ap_form DROP CONSTRAINT FK_7200EFA73D915CE3');
		$this->addSql('DROP SEQUENCE ap_form_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE ap_form_submission_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE ap_form_template_id_sequence CASCADE');
		$this->addSql('DROP TABLE ap_form');
		$this->addSql('DROP TABLE ap_form_submission');
		$this->addSql('DROP TABLE ap_form_template');
		$this->addSql('ALTER TABLE xtrf_user ADD hs_owner_id INT DEFAULT NULL');
	}
}
