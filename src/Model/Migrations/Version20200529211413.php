<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200529211413 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE dqa_report_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE dqa_issue_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE dqa_category_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE dqa_report (dqa_report_id BIGINT NOT NULL, activity_id BIGINT NOT NULL, proofer_name TEXT NOT NULL, page_count INT NOT NULL, format TEXT NOT NULL, score NUMERIC(19, 6) DEFAULT NULL, status TEXT NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(dqa_report_id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_606FF4B6AC74095A ON dqa_report (activity_id)');
		$this->addSql('CREATE INDEX IDX_606FF4B6A4F1F82 ON dqa_report (dqa_report_id)');
		$this->addSql('CREATE TABLE dqa_issue (id BIGINT NOT NULL, dqa_report_id BIGINT NOT NULL, dqa_category_id BIGINT NOT NULL, minor INT NOT NULL, major INT NOT NULL, critical INT NOT NULL, comment TEXT DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_D032F113A4F1F82 ON dqa_issue (dqa_report_id)');
		$this->addSql('CREATE INDEX IDX_D032F1138CBA9BFC ON dqa_issue (dqa_category_id)');
		$this->addSql('CREATE INDEX IDX_D032F113BF396750 ON dqa_issue (id)');
		$this->addSql('CREATE TABLE dqa_category (dqa_category_id BIGINT NOT NULL, parent_id BIGINT DEFAULT NULL, name TEXT NOT NULL, weight INT NOT NULL, is_leaf BOOLEAN NOT NULL, is_other BOOLEAN NOT NULL, path TEXT NOT NULL, path_depth TEXT NOT NULL, parent_name TEXT NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(dqa_category_id))');
		$this->addSql('CREATE INDEX IDX_9F8D3B6A727ACA70 ON dqa_category (parent_id)');
		$this->addSql('CREATE INDEX IDX_9F8D3B6A8CBA9BFC ON dqa_category (dqa_category_id)');
		$this->addSql('ALTER TABLE dqa_report ADD CONSTRAINT FK_606FF4B6AC74095A FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE dqa_issue ADD CONSTRAINT FK_D032F113A4F1F82 FOREIGN KEY (dqa_report_id) REFERENCES dqa_report (dqa_report_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE dqa_issue ADD CONSTRAINT FK_D032F1138CBA9BFC FOREIGN KEY (dqa_category_id) REFERENCES dqa_category (dqa_category_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE dqa_category ADD CONSTRAINT FK_9F8D3B6A727ACA70 FOREIGN KEY (parent_id) REFERENCES dqa_category (dqa_category_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE dqa_issue DROP CONSTRAINT FK_D032F113A4F1F82');
		$this->addSql('ALTER TABLE dqa_issue DROP CONSTRAINT FK_D032F1138CBA9BFC');
		$this->addSql('ALTER TABLE dqa_category DROP CONSTRAINT FK_9F8D3B6A727ACA70');
		$this->addSql('DROP SEQUENCE dqa_report_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE dqa_issue_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE dqa_category_id_sequence CASCADE');
		$this->addSql('DROP TABLE dqa_report');
		$this->addSql('DROP TABLE dqa_issue');
		$this->addSql('DROP TABLE dqa_category');
	}
}
