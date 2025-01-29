<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200812230047 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE wf_params_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE cron_job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE cron_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE wf_history (id BIGSERIAL NOT NULL, workflow_id BIGINT NOT NULL, name VARCHAR(255) DEFAULT NULL, total_files VARCHAR(255) DEFAULT NULL, processed_files VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, info TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, marking JSON DEFAULT NULL, context JSON DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cloud_name VARCHAR(255) DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, removed BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE TABLE wf_workflow (id BIGSERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, workflow_type VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE TABLE wf_params (id INT NOT NULL, workflow_id BIGINT DEFAULT NULL, params JSON NOT NULL, notification_target VARCHAR(255) DEFAULT NULL, source_disk VARCHAR(50) DEFAULT NULL, working_disk VARCHAR(50) DEFAULT NULL, expiration INT DEFAULT NULL, notification_type INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8FDE25802C7C2CBA ON wf_params (workflow_id)');
		$this->addSql('CREATE TABLE cron_job (id INT NOT NULL, name VARCHAR(191) NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(191) NOT NULL, description VARCHAR(191) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX un_name ON cron_job (name)');
		$this->addSql('CREATE TABLE cron_report (id INT NOT NULL, job_id INT DEFAULT NULL, run_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, run_time DOUBLE PRECISION NOT NULL, exit_code INT NOT NULL, output TEXT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_B6C6A7F5BE04EA9 ON cron_report (job_id)');
		$this->addSql('ALTER TABLE wf_params ADD CONSTRAINT FK_8FDE25802C7C2CBA FOREIGN KEY (workflow_id) REFERENCES wf_workflow (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE cron_report ADD CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE wf_params DROP CONSTRAINT FK_8FDE25802C7C2CBA');
		$this->addSql('ALTER TABLE cron_report DROP CONSTRAINT FK_B6C6A7F5BE04EA9');
		$this->addSql('DROP SEQUENCE wf_params_id_seq CASCADE');
		$this->addSql('DROP SEQUENCE cron_job_id_seq CASCADE');
		$this->addSql('DROP SEQUENCE cron_report_id_seq CASCADE');
		$this->addSql('DROP TABLE wf_history');
		$this->addSql('DROP TABLE wf_workflow');
		$this->addSql('DROP TABLE wf_params');
		$this->addSql('DROP TABLE cron_job');
		$this->addSql('DROP TABLE cron_report');
	}
}
