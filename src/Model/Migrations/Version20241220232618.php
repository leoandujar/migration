<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241220232618 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add missing columns to av_flow_step';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_step ADD next VARCHAR(80) DEFAULT NULL');
		$this->addSql('ALTER TABLE av_flow_step ADD starter BOOLEAN DEFAULT NULL');
		$this->addSql('ALTER TABLE av_flow_step DROP "order"');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_step ADD "order" INT NOT NULL');
		$this->addSql('ALTER TABLE av_flow_step DROP next');
		$this->addSql('ALTER TABLE av_flow_step DROP starter');
	}
}
