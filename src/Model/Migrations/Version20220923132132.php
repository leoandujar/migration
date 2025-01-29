<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220923132132 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_chart ADD name VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE av_chart ADD description VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE av_chart DROP type');
		$this->addSql('ALTER TABLE av_chart DROP mode');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_2877FD485E237E06 ON av_chart (name)');
		$this->addSql('DROP INDEX uniq_e27a8277153098de3d0a66');
		$this->addSql('DROP INDEX idx_e27a82de3d0a66');
		$this->addSql('ALTER TABLE av_report_type DROP category');
		$this->addSql('ALTER TABLE av_report_type RENAME COLUMN chart_type TO type');
		$this->addSql('CREATE INDEX IDX_E27A828CDE5729 ON av_report_type (type)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E27A82771530988CDE5729 ON av_report_type (code, type)');
		$this->addSql('ALTER TABLE av_chart RENAME TO av_report_chart');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX IDX_E27A828CDE5729');
		$this->addSql('DROP INDEX UNIQ_E27A82771530988CDE5729');
		$this->addSql('ALTER TABLE av_report_type ADD category VARCHAR(50) DEFAULT NULL');
		$this->addSql('ALTER TABLE av_report_type RENAME COLUMN type TO chart_type');
		$this->addSql('CREATE UNIQUE INDEX uniq_e27a8277153098de3d0a66 ON av_report_type (code, chart_type)');
		$this->addSql('CREATE INDEX idx_e27a82de3d0a66 ON av_report_type (chart_type)');
		$this->addSql('DROP INDEX UNIQ_2877FD485E237E06');
		$this->addSql('ALTER TABLE av_chart ADD type VARCHAR(50) DEFAULT \'widget\' NOT NULL');
		$this->addSql('ALTER TABLE av_chart ADD mode VARCHAR(50) DEFAULT NULL');
		$this->addSql('ALTER TABLE av_chart DROP name');
		$this->addSql('ALTER TABLE av_chart DROP description');
		$this->addSql('ALTER TABLE av_report_chart RENAME TO av_chart');
	}
}
