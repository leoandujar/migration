<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230130123848 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE parameter_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE av_parameter (parameter_id BIGINT NOT NULL, name VARCHAR(50) NOT NULL, scope VARCHAR(150) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(parameter_id))');
		$this->addSql('CREATE INDEX IDX_5A3426C35E237E06 ON av_parameter (name)');
		$this->addSql('CREATE INDEX IDX_5A3426C364C19C1 ON av_parameter (scope)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_5A3426C35E237E0664C19C1 ON av_parameter (name, scope)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE parameter_id_sequence CASCADE');
		$this->addSql('DROP TABLE av_parameter');
	}
}
