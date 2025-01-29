<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200718114143 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE permission ADD cp_user_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA9FFDF951 FOREIGN KEY (cp_user_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_E04992AA9FFDF951 ON permission (cp_user_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AA9FFDF951');
		$this->addSql('DROP INDEX IDX_E04992AA9FFDF951');
		$this->addSql('ALTER TABLE permission DROP cp_user_id');
	}
}
