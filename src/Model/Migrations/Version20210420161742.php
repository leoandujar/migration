<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210420161742 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_engagement ALTER "timestamp" TYPE BIGINT');
		$this->addSql('ALTER TABLE hs_engagement ALTER "timestamp" DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_engagement ALTER "timestamp" DROP NOT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('ALTER TABLE hs_engagement ALTER timestamp TYPE INT');
		$this->addSql('ALTER TABLE hs_engagement ALTER timestamp DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_engagement ALTER timestamp SET NOT NULL');
	}
}
