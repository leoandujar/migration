<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319135333 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE cp_setting_project ADD categories JSON DEFAULT NULL');
		$this->addSql('ALTER TABLE cp_setting_project ADD rush_deadline INT DEFAULT 24');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE cp_setting_project DROP categories');
		$this->addSql('ALTER TABLE cp_setting_project DROP rush_deadline');
	}
}
