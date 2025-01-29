<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220928154410 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_report_chart ADD type INT DEFAULT 1 NOT NULL');
		$this->addSql('ALTER TABLE av_report_chart ADD return_y VARCHAR(50) DEFAULT NULL');
		$this->addSql('DROP INDEX idx_e27a8277153098');
		$this->addSql('DROP INDEX idx_e27a828cde5729');
		$this->addSql('DROP INDEX uniq_e27a82771530988cde5729');
		$this->addSql('ALTER TABLE av_report_type DROP code');
		$this->addSql('ALTER TABLE av_report_type DROP type');
		$this->addSql('ALTER TABLE av_report_type DROP return_y');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_report_type ADD code VARCHAR(150) NOT NULL');
		$this->addSql('ALTER TABLE av_report_type ADD type INT DEFAULT 1 NOT NULL');
		$this->addSql('ALTER TABLE av_report_type ADD return_y VARCHAR(50) DEFAULT NULL');
		$this->addSql('CREATE INDEX idx_e27a8277153098 ON av_report_type (code)');
		$this->addSql('CREATE INDEX idx_e27a828cde5729 ON av_report_type (type)');
		$this->addSql('CREATE UNIQUE INDEX uniq_e27a82771530988cde5729 ON av_report_type (code, type)');
		$this->addSql('ALTER TABLE av_report_chart DROP type');
		$this->addSql('ALTER TABLE av_report_chart DROP return_y');
	}
}
