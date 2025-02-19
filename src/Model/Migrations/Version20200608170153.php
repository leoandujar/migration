<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200608170153 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('CREATE INDEX IDX_57698A6A5E237E06 ON role (name)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A77153098 ON role (code)');
		$this->addSql('CREATE INDEX IDX_47CC8C925E237E06 ON action (name)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_47CC8C9277153098 ON action (code)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX IDX_57698A6A5E237E06');
		$this->addSql('DROP INDEX UNIQ_57698A6A77153098');
		$this->addSql('DROP INDEX IDX_47CC8C925E237E06');
		$this->addSql('DROP INDEX UNIQ_47CC8C9277153098');
	}
}
