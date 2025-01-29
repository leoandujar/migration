<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210506232246 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE dqa_issue RENAME TO quality_issue');
		$this->addSql('ALTER TABLE dqa_report RENAME TO quality_report');
		$this->addSql('ALTER TABLE dqa_category RENAME TO quality_category');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE quality_issue RENAME TO dqa_issue');
		$this->addSql('ALTER TABLE quality_report RENAME TO dqa_report');
		$this->addSql('ALTER TABLE quality_category RENAME TO dqa_category');
	}
}
