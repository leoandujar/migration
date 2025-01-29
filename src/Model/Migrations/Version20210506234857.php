<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210506234857 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER SEQUENCE dqa_category_id_sequence RENAME TO quality_category_id_sequence');
		$this->addSql('ALTER SEQUENCE dqa_issue_id_sequence RENAME TO quality_issue_id_sequence');
		$this->addSql('ALTER SEQUENCE dqa_report_id_sequence RENAME TO quality_report_id_sequence');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER SEQUENCE quality_category_id_sequence RENAME TO dqa_category_id_sequence;');
		$this->addSql('ALTER SEQUENCE quality_issue_id_sequence RENAME TO dqa_issue_id_sequence;');
		$this->addSql('ALTER SEQUENCE quality_report_id_sequence RENAME TO dqa_report_id_sequence;');
	}
}
