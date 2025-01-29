<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210423141031 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_form_template ALTER type TYPE INT USING type::integer');
		$this->addSql('ALTER TABLE ap_form_template ALTER type DROP DEFAULT');
		$this->addSql('ALTER TABLE ap_form_template ALTER type DROP NOT NULL');
		$this->addSql('ALTER TABLE ap_form_template ALTER type TYPE INT');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE ap_form_template ALTER type TYPE VARCHAR(30)');
		$this->addSql('ALTER TABLE ap_form_template ALTER type DROP DEFAULT');
		$this->addSql('ALTER TABLE ap_form_template ALTER type SET NOT NULL');
	}
}
