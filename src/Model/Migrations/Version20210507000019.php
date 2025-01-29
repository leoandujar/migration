<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507000019 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE quality_category RENAME COLUMN dqa_category_id TO quality_category_id');
		$this->addSql('ALTER TABLE quality_report RENAME COLUMN dqa_report_id TO quality_report_id');
		$this->addSql('ALTER TABLE quality_issue RENAME COLUMN dqa_category_id TO quality_category_id');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE quality_category RENAME COLUMN quality_category_id TO dqa_category_id');
		$this->addSql('ALTER TABLE quality_issue RENAME COLUMN quality_report_id TO dqa_report_id');
		$this->addSql('ALTER TABLE quality_issue RENAME COLUMN quality_category_id TO dqa_category_id');
	}
}
