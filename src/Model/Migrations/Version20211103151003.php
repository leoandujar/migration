<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211103151003 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_report_type ADD parent BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_report_type ALTER function_name SET NOT NULL');
		$this->addSql('ALTER TABLE av_report_type ADD CONSTRAINT FK_E27A823D8E604F FOREIGN KEY (parent) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_E27A823D8E604F ON av_report_type (parent)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_report_type DROP CONSTRAINT FK_E27A823D8E604F');
		$this->addSql('DROP INDEX IDX_E27A823D8E604F');
		$this->addSql('ALTER TABLE av_report_type DROP parent');
		$this->addSql('ALTER TABLE av_report_type ALTER function_name DROP NOT NULL');
	}
}
