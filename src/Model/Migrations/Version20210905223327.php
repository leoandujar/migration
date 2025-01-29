<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210905223327 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE av_report_history_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE av_report_template_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE av_report_type_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE av_report_history (av_report_history_id BIGINT NOT NULL, av_report_template_id BIGINT NOT NULL, contact_person_id BIGINT NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(av_report_history_id))');
		$this->addSql('CREATE INDEX IDX_FDD2E42E381AC2C0 ON av_report_history (av_report_template_id)');
		$this->addSql('CREATE INDEX IDX_FDD2E42E4F8A983C ON av_report_history (contact_person_id)');
		$this->addSql('CREATE TABLE av_report_template (av_report_template_id BIGINT NOT NULL, name VARCHAR(50) NOT NULL, report_types JSON NOT NULL, filters JSON DEFAULT NULL, format INT NOT NULL, template TEXT DEFAULT NULL, PRIMARY KEY(av_report_template_id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_AA62E2C05E237E06 ON av_report_template (name)');
		$this->addSql('CREATE TABLE av_report_type (av_report_type_id BIGINT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(150) NOT NULL, description VARCHAR(255) NOT NULL, chart_type INT DEFAULT 1 NOT NULL, PRIMARY KEY(av_report_type_id))');
		$this->addSql('CREATE INDEX IDX_E27A8277153098 ON av_report_type (code)');
		$this->addSql('CREATE INDEX IDX_E27A82DE3D0A66 ON av_report_type (chart_type)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E27A825E237E06 ON av_report_type (name)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E27A8277153098DE3D0A66 ON av_report_type (code, chart_type)');
		$this->addSql('ALTER TABLE av_report_history ADD CONSTRAINT FK_FDD2E42E381AC2C0 FOREIGN KEY (av_report_template_id) REFERENCES av_report_template (av_report_template_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_report_history ADD CONSTRAINT FK_FDD2E42E4F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_report_history DROP CONSTRAINT FK_FDD2E42E381AC2C0');
		$this->addSql('DROP SEQUENCE av_report_history_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE av_report_template_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE av_report_type_id_sequence CASCADE');
		$this->addSql('DROP TABLE av_report_history');
		$this->addSql('DROP TABLE av_report_template');
		$this->addSql('DROP TABLE av_report_type');
	}
}
