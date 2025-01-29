<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425141337 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_dashboard DROP height');
		$this->addSql('ALTER TABLE av_dashboard DROP width');
		$this->addSql('ALTER TABLE av_dashboard DROP axis_x');
		$this->addSql('ALTER TABLE av_dashboard DROP axis_y');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_dashboard ADD height INT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_dashboard ADD width INT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_dashboard ADD axis_x INT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_dashboard ADD axis_y INT DEFAULT NULL');
	}
}
