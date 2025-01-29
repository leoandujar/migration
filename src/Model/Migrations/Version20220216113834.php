<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220216113834 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE bl_rate_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE bl_rate (id BIGINT NOT NULL, bl_rate_id BIGINT NOT NULL, enabled BOOLEAN DEFAULT NULL, communication_type_id INT NOT NULL, source_language_id INT NOT NULL, target_language_id INT NOT NULL, rate NUMERIC(19, 6) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_BA98F5AC213C6F00 ON bl_rate (bl_rate_id)');
		$this->addSql('ALTER TABLE bl_call ADD bl_rate_id BIGINT DEFAULT NULL');
		$this->addSql('CREATE INDEX IDX_A9FAE5AB213C6F00 ON bl_call (bl_rate_id)');
		$this->addSql('ALTER TABLE bl_call ADD customer_duration INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD additional JSONB DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD requester VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB213C6F00 FOREIGN KEY (bl_rate_id) REFERENCES bl_rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

		$this->addSql('ALTER TABLE bl_call ADD duration_minimal BOOLEAN DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD duration_seconds INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD duration_minutes INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD duration_hours INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD routing_amount NUMERIC(19, 6) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_amount NUMERIC(19, 6) DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5AB213C6F00');
		$this->addSql('DROP SEQUENCE bl_rate_id_sequence CASCADE');
		$this->addSql('DROP TABLE bl_rate');
		$this->addSql('ALTER TABLE bl_call DROP bl_rate_id');
		$this->addSql('ALTER TABLE bl_call DROP customer_duration');
		$this->addSql('ALTER TABLE bl_call DROP additional');
		$this->addSql('ALTER TABLE bl_call DROP requester');

		$this->addSql('ALTER TABLE bl_call DROP duration_minimal');
		$this->addSql('ALTER TABLE bl_call DROP duration_seconds');
		$this->addSql('ALTER TABLE bl_call DROP duration_minutes');
		$this->addSql('ALTER TABLE bl_call DROP duration_hours');
		$this->addSql('ALTER TABLE bl_call DROP routing_amount');
		$this->addSql('ALTER TABLE bl_call DROP bl_amount');
	}
}
