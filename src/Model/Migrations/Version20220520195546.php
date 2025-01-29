<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220520195546 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE xtm_edit_distance ADD rows_count INT NOT NULL');
		$this->addSql('ALTER TABLE xtm_edit_distance ADD rows_zero_count INT NOT NULL');
		$this->addSql('ALTER TABLE xtm_edit_distance DROP segments_count');
		$this->addSql('ALTER TABLE xtm_edit_distance DROP segments_zero_count');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE xtm_edit_distance ADD segments_count INT NOT NULL');
		$this->addSql('ALTER TABLE xtm_edit_distance ADD segments_zero_count INT NOT NULL');
		$this->addSql('ALTER TABLE xtm_edit_distance DROP rows_count');
		$this->addSql('ALTER TABLE xtm_edit_distance DROP rows_zero_count');
	}
}
