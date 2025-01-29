<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240227213618 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		
		$this->addSql('ALTER TABLE cp_setting_project ADD IF NOT EXISTS files_queue VARCHAR(60) DEFAULT \'projects_quotes_normal\' NOT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cp_setting_project DROP files_queue');
	}
}
