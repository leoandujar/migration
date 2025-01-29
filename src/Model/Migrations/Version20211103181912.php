<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211103181912 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE chart ADD report_type BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE chart ADD CONSTRAINT FK_E5562A2AFFF2BAD2 FOREIGN KEY (report_type) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_E5562A2AFFF2BAD2 ON chart (report_type)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE chart DROP CONSTRAINT FK_E5562A2AFFF2BAD2');
		$this->addSql('DROP INDEX IDX_E5562A2AFFF2BAD2');
		$this->addSql('ALTER TABLE chart DROP report_type');
		$this->addSql('ALTER INDEX uniq_1f7156cb5da0fb8a5d5f193 RENAME TO uniq_5a49d2b55da0fb8a5d5f193');
	}
}
