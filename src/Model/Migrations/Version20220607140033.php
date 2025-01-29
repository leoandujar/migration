<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220607140033 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_form_submission ADD owner_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE ap_form_submission ADD collaborators JSONB DEFAULT NULL');
		$this->addSql('ALTER TABLE ap_form_submission ADD CONSTRAINT FK_4894C5517E3C61F9 FOREIGN KEY (owner_id) REFERENCES internal_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_4894C5517E3C61F9 ON ap_form_submission (owner_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_form_submission DROP CONSTRAINT FK_4894C5517E3C61F9');
		$this->addSql('DROP INDEX IDX_4894C5517E3C61F9');
		$this->addSql('ALTER TABLE ap_form_submission DROP owner_id');
		$this->addSql('ALTER TABLE ap_form_submission DROP collaborators');
	}
}
