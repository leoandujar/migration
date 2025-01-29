<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212215545 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Category field added to av_flow_step';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_step ADD category VARCHAR(20) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_step DROP category');
	}
}
