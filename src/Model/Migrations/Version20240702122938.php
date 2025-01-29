<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240702122938 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'To add password_updated_date column to system_account table.';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE system_account ADD password_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE system_account DROP password_updated_at');
	}
}
