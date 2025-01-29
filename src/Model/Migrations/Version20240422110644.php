<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422110644 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_dashboard ADD height INT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_dashboard ADD width INT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_dashboard ADD axis_x INT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_dashboard ADD axis_y INT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_dashboard DROP height');
		$this->addSql('ALTER TABLE av_dashboard DROP width');
		$this->addSql('ALTER TABLE av_dashboard DROP axis_x');
		$this->addSql('ALTER TABLE av_dashboard DROP axis_y');
	}
}
