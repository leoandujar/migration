<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200608111717 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE internal_user_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE internal_user (internal_user_id_sequence BIGINT NOT NULL, username VARCHAR(50) NOT NULL, status INT NOT NULL, email VARCHAR(64) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, password VARCHAR(200) NOT NULL, salt VARCHAR(60) DEFAULT NULL, roles JSON DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, api_key TEXT DEFAULT NULL, PRIMARY KEY(internal_user_id_sequence))');
		$this->addSql('CREATE INDEX IDX_61134782A8EEDB5F ON internal_user (internal_user_id_sequence)');
		$this->addSql('CREATE INDEX IDX_611347827B00651C ON internal_user (status)');
		$this->addSql('CREATE INDEX IDX_61134782B23DB7B8 ON internal_user (created)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_61134782E7927C74 ON internal_user (email)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_61134782F85E0677 ON internal_user (username)');
		$this->addSql('ALTER TABLE role DROP CONSTRAINT fk_57698a6a727aca70');
		$this->addSql('DROP INDEX idx_57698a6a727aca70');
		$this->addSql('ALTER TABLE role ADD code VARCHAR(150) NOT NULL');
		$this->addSql('ALTER TABLE role DROP parent_id');
		$this->addSql('ALTER INDEX uniq_a7e7925c81398e09 RENAME TO UNIQ_A7E7925C9395C3F3');
		$this->addSql('ALTER TABLE action RENAME COLUMN slug TO code');
		$this->addSql('ALTER TABLE permission DROP CONSTRAINT fk_e04992aaa76ed395');
		$this->addSql('DROP INDEX idx_e04992aaa76ed395');
		$this->addSql('ALTER TABLE permission RENAME COLUMN user_id TO internal_user_id');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AABF7692A3 FOREIGN KEY (internal_user_id) REFERENCES internal_user (internal_user_id_sequence) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_E04992AABF7692A3 ON permission (internal_user_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AABF7692A3');
		$this->addSql('DROP SEQUENCE internal_user_id_sequence CASCADE');
		$this->addSql('DROP TABLE internal_user');
		$this->addSql('ALTER INDEX uniq_a7e7925c9395c3f3 RENAME TO uniq_a7e7925c81398e09');
		$this->addSql('ALTER TABLE role ADD parent_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE role DROP code');
		$this->addSql('ALTER TABLE role ADD CONSTRAINT fk_57698a6a727aca70 FOREIGN KEY (parent_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_57698a6a727aca70 ON role (parent_id)');
		$this->addSql('DROP INDEX IDX_E04992AABF7692A3');
		$this->addSql('ALTER TABLE permission RENAME COLUMN internal_user_id TO user_id');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT fk_e04992aaa76ed395 FOREIGN KEY (user_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_e04992aaa76ed395 ON permission (user_id)');
		$this->addSql('ALTER TABLE action RENAME COLUMN code TO slug');
	}
}
