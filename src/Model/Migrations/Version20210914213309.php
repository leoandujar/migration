<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210914213309 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE av_piv_report_template_type_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE av_piv_reports_templates_type (av_piv_report_template_type_id BIGINT NOT NULL, template_id BIGINT NOT NULL, report_type_id BIGINT NOT NULL, PRIMARY KEY(av_piv_report_template_type_id))');
		$this->addSql('CREATE INDEX IDX_5A49D2B55DA0FB8 ON av_piv_reports_templates_type (template_id)');
		$this->addSql('CREATE INDEX IDX_5A49D2B5A5D5F193 ON av_piv_reports_templates_type (report_type_id)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_5A49D2B55DA0FB8A5D5F193 ON av_piv_reports_templates_type (template_id, report_type_id)');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type ADD CONSTRAINT FK_5A49D2B55DA0FB8 FOREIGN KEY (template_id) REFERENCES av_report_template (av_report_template_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type ADD CONSTRAINT FK_5A49D2B5A5D5F193 FOREIGN KEY (report_type_id) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_report_template DROP report_types');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE av_piv_report_template_type_id_sequence CASCADE');
		$this->addSql('DROP TABLE av_piv_reports_templates_type');
		$this->addSql('ALTER TABLE av_report_template ADD report_types JSON NOT NULL');
	}
}
