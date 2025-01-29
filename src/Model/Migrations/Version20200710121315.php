<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200710121315 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE metrics ADD no_match_characters INT DEFAULT NULL');
		$this->addSql('ALTER TABLE metrics ADD no_match_units INT DEFAULT NULL');
		$this->addSql('ALTER TABLE metrics ADD no_match_white_spaces INT DEFAULT NULL');
		$this->addSql('ALTER TABLE metrics ADD no_match_words INT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE metrics DROP no_match_characters');
		$this->addSql('ALTER TABLE metrics DROP no_match_units');
		$this->addSql('ALTER TABLE metrics DROP no_match_white_spaces');
		$this->addSql('ALTER TABLE metrics DROP no_match_words');
	}
}
