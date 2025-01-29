<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405000307 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task ADD working_files_number INT DEFAULT NULL');
		$this->addSql('ALTER TABLE task ADD ontime_status VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE task ADD partial_delivery_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task DROP working_files_number');
		$this->addSql('ALTER TABLE task DROP ontime_status');
		$this->addSql('ALTER TABLE task DROP partial_delivery_date');
	}
}
