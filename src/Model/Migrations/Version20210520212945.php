<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520212945 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE time_serie_stats (time TIMESTAMP(0) WITH TIME ZONE NOT NULL, customer_id BIGINT DEFAULT NULL, project_manager_id BIGINT DEFAULT NULL, coordinator_manager_id BIGINT DEFAULT NULL, open_projects INT NOT NULL, open_tasks INT NOT NULL, requested_quotes INT NOT NULL, total_agreed NUMERIC(19, 6) NOT NULL, total_cost NUMERIC(19, 6) NOT NULL, total_words INT NOT NULL, total_working_files INT NOT NULL, PRIMARY KEY(time))');
		$this->addSql('CREATE INDEX IDX_417857D79395C3F3 ON time_serie_stats (customer_id)');
		$this->addSql('CREATE INDEX IDX_417857D760984F51 ON time_serie_stats (project_manager_id)');
		$this->addSql('CREATE INDEX IDX_417857D7DDA81D53 ON time_serie_stats (coordinator_manager_id)');
		$this->addSql('ALTER TABLE time_serie_stats ADD CONSTRAINT FK_417857D79395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE time_serie_stats ADD CONSTRAINT FK_417857D760984F51 FOREIGN KEY (project_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE time_serie_stats ADD CONSTRAINT FK_417857D7DDA81D53 FOREIGN KEY (coordinator_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE time_serie_stats');
	}
}
