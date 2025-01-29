<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507132756 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE quality_category RENAME constraint dqa_category_pkey to quality_category_pkey');
		$this->addSql('ALTER TABLE quality_issue RENAME constraint dqa_issue_pkey to quality_issue_pkey');
		$this->addSql('ALTER TABLE quality_report RENAME constraint dqa_report_pkey to quality_report_pkey');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE quality_category RENAME constraint quality_category_pkey to dqa_category_pkey');
		$this->addSql('ALTER TABLE quality_issue RENAME constraint quality_issue_pkey to dqa_issue_pkey');
		$this->addSql('ALTER TABLE quality_report RENAME constraint quality_report_pkey to dqa_report_pkey');
	}
}
