<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521144350 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE quality_answer_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE quality_answer (quality_answer_id BIGINT NOT NULL, quality_category_id BIGINT NOT NULL, label VARCHAR(255) NOT NULL, score INT NOT NULL, PRIMARY KEY(quality_answer_id))');
		$this->addSql('CREATE INDEX IDX_651985FBFF562E61 ON quality_answer (quality_category_id)');
		$this->addSql('CREATE INDEX IDX_651985FBFD4F9DAA ON quality_answer (quality_answer_id)');
		$this->addSql('ALTER TABLE quality_answer ADD CONSTRAINT FK_651985FBFF562E61 FOREIGN KEY (quality_category_id) REFERENCES quality_category (quality_category_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE quality_category ADD required BOOLEAN DEFAULT \'false\' NOT NULL');
		$this->addSql('ALTER TABLE quality_report ADD excellent BOOLEAN DEFAULT \'false\' NOT NULL');
		$this->addSql('ALTER TABLE quality_report ADD comment VARCHAR(255) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('DROP SEQUENCE quality_answer_id_sequence CASCADE');
		$this->addSql('DROP TABLE quality_answer');
		$this->addSql('ALTER TABLE quality_report DROP excellent');
		$this->addSql('ALTER TABLE quality_report DROP comment');
		$this->addSql('ALTER TABLE quality_category DROP required');
		$this->addSql('ALTER TABLE quality_category ALTER type SET DEFAULT \'DQA\'');
	}
}
