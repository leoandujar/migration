<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221004201955 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX uniq_e5562a2a77153098');
		$this->addSql('ALTER TABLE av_report_chart RENAME COLUMN code TO slug');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A4DE6C67989D9B62 ON av_report_chart (slug)');
		$this->addSql('ALTER TABLE av_report_type ADD code VARCHAR(255) DEFAULT NULL');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E27A8277153098 ON av_report_type (code)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX UNIQ_E27A8277153098');
		$this->addSql('ALTER TABLE av_report_type DROP code');
		$this->addSql('DROP INDEX UNIQ_A4DE6C67989D9B62');
		$this->addSql('ALTER TABLE av_report_chart RENAME COLUMN slug TO code');
		$this->addSql('CREATE UNIQUE INDEX uniq_e5562a2a77153098 ON av_report_chart (code)');
	}
}
