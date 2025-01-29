<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322101918 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'AvDashboard entity migration';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs

		$this->addSql('CREATE TABLE av_dashboard (av_dashboard_id UUID NOT NULL, contact_person_id BIGINT DEFAULT NULL, av_report_chart_id UUID DEFAULT NULL, options JSONB DEFAULT NULL, PRIMARY KEY(av_dashboard_id))');
		$this->addSql('CREATE INDEX IDX_F76907E24F8A983C ON av_dashboard (contact_person_id)');
		$this->addSql('CREATE INDEX IDX_F76907E283FAC349 ON av_dashboard (av_report_chart_id)');
		$this->addSql('ALTER TABLE av_dashboard ADD CONSTRAINT FK_F76907E24F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_dashboard ADD CONSTRAINT FK_F76907E283FAC349 FOREIGN KEY (av_report_chart_id) REFERENCES av_report_chart (av_report_chart_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs

		$this->addSql('ALTER TABLE av_dashboard DROP CONSTRAINT FK_F76907E24F8A983C');
		$this->addSql('ALTER TABLE av_dashboard DROP CONSTRAINT FK_F76907E283FAC349');
		$this->addSql('DROP TABLE av_dashboard');
	}
}
