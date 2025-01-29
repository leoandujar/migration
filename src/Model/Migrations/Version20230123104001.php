<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123104001 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE provider DROP ref_date_2');
		$this->addSql('ALTER TABLE provider DROP evaluation_responsiveness_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_ontime_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_collaboration_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_instructions_average');
		$this->addSql('ALTER TABLE provider DROP evaluation_quarter_total_average');
		$this->addSql('ALTER TABLE service ADD management_mode VARCHAR(255) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE provider ADD ref_date_2 TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_responsiveness_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_ontime_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_collaboration_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_instructions_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE provider ADD evaluation_quarter_total_average VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE service DROP management_mode');
	}
}
