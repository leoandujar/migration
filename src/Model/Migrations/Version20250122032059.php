<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250122032059 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Added inputsOnStart to FlowAction and slug to FlowAction';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_action ADD inputsOnStart JSONB DEFAULT NULL');
		$this->addSql('ALTER TABLE av_flow_action RENAME COLUMN next TO slug');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_action DROP inputsOnStart');
		$this->addSql('ALTER TABLE av_flow_action RENAME COLUMN slug TO next');
	}
}
