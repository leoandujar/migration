<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018175310 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE av_category_group (id UUID NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(150) NOT NULL, target INT DEFAULT 1 NOT NULL, active BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_3F02E2E977153098 ON av_category_group (code)');
		$this->addSql('CREATE INDEX IDX_3F02E2E95E237E06 ON av_category_group (name)');
		$this->addSql('CREATE INDEX IDX_3F02E2E9466F2FFC ON av_category_group (target)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_3F02E2E977153098466F2FFC ON av_category_group (code, target)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE av_category_group_id_sequence CASCADE');
		$this->addSql('DROP TABLE av_category_group');
	}
}
