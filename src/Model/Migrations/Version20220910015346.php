<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220910015346 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_chart DROP CONSTRAINT fk_e5562a2afff2bad2');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT fk_c118dcc0e5562a2b');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts DROP CONSTRAINT fk_1f7156cba5d5f193');
		$this->addSql('DROP INDEX idx_1f7156cba5d5f193');
		$this->addSql('DROP INDEX uniq_1f7156cb5da0fb8a5d5f193');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ADD chart_id UUID DEFAULT NULL');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts DROP report_type_id');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ADD CONSTRAINT FK_DB1F62A0BEF83E0A FOREIGN KEY (chart_id) REFERENCES av_chart (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_DB1F62A0BEF83E0A ON av_piv_reports_templates_charts (chart_id)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_DB1F62A05DA0FB8BEF83E0A ON av_piv_reports_templates_charts (template_id, chart_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_piv_reports_templates_charts DROP CONSTRAINT FK_DB1F62A0BEF83E0A');
		$this->addSql('DROP INDEX IDX_DB1F62A0BEF83E0A');
		$this->addSql('DROP INDEX UNIQ_DB1F62A05DA0FB8BEF83E0A');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ADD report_type_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts DROP chart_id');
		$this->addSql('ALTER TABLE av_piv_reports_templates_charts ADD CONSTRAINT fk_1f7156cba5d5f193 FOREIGN KEY (report_type_id) REFERENCES av_report_type (av_report_type_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_1f7156cba5d5f193 ON av_piv_reports_templates_charts (report_type_id)');
		$this->addSql('CREATE UNIQUE INDEX uniq_1f7156cb5da0fb8a5d5f193 ON av_piv_reports_templates_charts (template_id, report_type_id)');
	}
}
