<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220730212344 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE ap_template (cp_template_id BIGINT NOT NULL, internal_user_id BIGINT NOT NULL, name VARCHAR(50) NOT NULL, target_entity INT DEFAULT 1 NOT NULL, data JSON NOT NULL, PRIMARY KEY(cp_template_id))');
		$this->addSql('CREATE INDEX IDX_533D33C1BB41D498 ON ap_template (cp_template_id)');
		$this->addSql('CREATE INDEX IDX_533D33C1BF7692A3 ON ap_template (internal_user_id)');
		$this->addSql('CREATE INDEX IDX_533D33C15E237E06 ON ap_template (name)');
		$this->addSql('CREATE INDEX IDX_533D33C1722A3D8 ON ap_template (target_entity)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_533D33C15E237E06BF7692A3 ON ap_template (name, internal_user_id)');
		$this->addSql('ALTER TABLE ap_template ADD CONSTRAINT FK_533D33C1BF7692A3 FOREIGN KEY (internal_user_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE ap_template');
	}
}
