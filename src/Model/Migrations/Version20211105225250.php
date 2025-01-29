<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211105225250 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_piv_reports_templates_type DROP CONSTRAINT fk_5a49d2b55da0fb8');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type DROP CONSTRAINT fk_5a49d2b5a5d5f193');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type ADD CONSTRAINT FK_1F7156CB5DA0FB8 FOREIGN KEY (template_id) REFERENCES av_report_template (av_report_template_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type ADD CONSTRAINT FK_1F7156CBA5D5F193 FOREIGN KEY (report_type_id) REFERENCES av_report_type (av_report_type_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER INDEX idx_5a49d2b55da0fb8 RENAME TO IDX_1F7156CB5DA0FB8');
		$this->addSql('ALTER INDEX idx_5a49d2b5a5d5f193 RENAME TO IDX_1F7156CBA5D5F193');
		$this->addSql('ALTER INDEX uniq_5a49d2b55da0fb8a5d5f193 RENAME TO UNIQ_1F7156CB5DA0FB8A5D5F193');
		$this->addSql('ALTER TABLE av_report_template ALTER template TYPE VARCHAR(50)');
		$this->addSql('ALTER TABLE av_report_template ALTER template DROP DEFAULT');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_report_template ALTER template TYPE TEXT');
		$this->addSql('ALTER TABLE av_report_template ALTER template DROP DEFAULT');
		$this->addSql('ALTER TABLE av_report_template ALTER template TYPE TEXT');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type DROP CONSTRAINT FK_1F7156CB5DA0FB8');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type DROP CONSTRAINT FK_1F7156CBA5D5F193');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type ADD CONSTRAINT fk_5a49d2b55da0fb8 FOREIGN KEY (template_id) REFERENCES av_report_template (av_report_template_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_piv_reports_templates_type ADD CONSTRAINT fk_5a49d2b5a5d5f193 FOREIGN KEY (report_type_id) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER INDEX idx_1f7156cba5d5f193 RENAME TO idx_5a49d2b5a5d5f193');
		$this->addSql('ALTER INDEX idx_1f7156cb5da0fb8 RENAME TO idx_5a49d2b55da0fb8');
		$this->addSql('ALTER INDEX uniq_1f7156cb5da0fb8a5d5f193 RENAME TO uniq_5a49d2b55da0fb8a5d5f193');
	}
}
