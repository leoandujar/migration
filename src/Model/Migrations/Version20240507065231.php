<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507065231 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'To add isDearchive column to cp_setting_project table';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cp_setting_project ADD dearchive BOOLEAN DEFAULT false');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cp_setting_project DROP dearchive');
	}
}
