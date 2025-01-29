<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520214608 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		$this->addSql("SELECT create_hypertable('time_serie_stats', 'time')");
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
	}
}
