<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118161332 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE xtrf_user_entity_image (xtrf_user_id BIGINT NOT NULL, image_data BYTEA DEFAULT NULL, PRIMARY KEY(xtrf_user_id))');
		$this->addSql('CREATE INDEX IDX_719F9A497CA9501E ON xtrf_user_entity_image (xtrf_user_id)');
		$this->addSql('ALTER TABLE xtrf_user_entity_image ADD CONSTRAINT FK_719F9A497CA9501E FOREIGN KEY (xtrf_user_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE xtrf_user_entity_image');
	}
}
