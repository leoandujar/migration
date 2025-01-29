<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924205320 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE wf_history ALTER processed_files TYPE TEXT');
		$this->addSql('ALTER TABLE wf_history ALTER processed_files DROP DEFAULT');
		$this->addSql('ALTER TABLE wf_history ALTER cloud_name TYPE VARCHAR(1000)');
		$this->addSql('ALTER TABLE wf_params ALTER notification_target TYPE VARCHAR(2048)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE wf_params ALTER notification_target TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE wf_history ALTER processed_files TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE wf_history ALTER processed_files DROP DEFAULT');
		$this->addSql('ALTER TABLE wf_history ALTER cloud_name TYPE VARCHAR(255)');
	}
}
