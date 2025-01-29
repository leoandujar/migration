<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314090056 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'to add custom_fields_new column to cp_setting_project table.';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cp_setting_project ADD custom_fields_new JSON DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cp_setting_project DROP custom_fields_new');
	}
}
