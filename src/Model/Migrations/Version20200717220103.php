<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200717220103 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX uniq_57698a6a77153098');
		$this->addSql('CREATE INDEX IDX_57698A6A77153098 ON role (code)');
		$this->addSql('CREATE INDEX IDX_57698A6A466F2FFC ON role (target)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A77153098466F2FFC ON role (code, target)');

		$this->addSql('DROP INDEX uniq_47cc8c9277153098');
		$this->addSql('CREATE INDEX IDX_47CC8C9277153098 ON action (code)');
		$this->addSql('CREATE INDEX IDX_47CC8C92466F2FFC ON action (target)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_47CC8C9277153098466F2FFC ON action (code, target)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP INDEX IDX_57698A6A77153098');
		$this->addSql('DROP INDEX IDX_57698A6A466F2FFC');
		$this->addSql('DROP INDEX UNIQ_57698A6A77153098466F2FFC');
		$this->addSql('CREATE UNIQUE INDEX uniq_57698a6a77153098 ON role (code)');

		$this->addSql('DROP INDEX IDX_47CC8C9277153098');
		$this->addSql('DROP INDEX IDX_47CC8C92466F2FFC');
		$this->addSql('DROP INDEX UNIQ_47CC8C9277153098466F2FFC');
		$this->addSql('CREATE UNIQUE INDEX uniq_47cc8c9277153098 ON action (code)');
	}
}
