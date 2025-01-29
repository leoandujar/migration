<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221104004621 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER INDEX IF EXISTS idx_24c07ba765c59816 RENAME TO IDX_AA80376E65C59816');
		$this->addSql('ALTER INDEX IF EXISTS idx_24c07ba785f30b8c RENAME TO IDX_AA80376E85F30B8C');
		$this->addSql('ALTER INDEX IF EXISTS idx_2c79cc7fde12ab56 RENAME TO IDX_220570FADE12AB56');
		$this->addSql('ALTER INDEX IF EXISTS idx_2c79cc7f65c59816 RENAME TO IDX_220570FA65C59816');
		$this->addSql('ALTER INDEX IF EXISTS idx_2c79cc7f777abbab RENAME TO IDX_220570FA8147BE6D');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER INDEX IF EXISTS idx_220570fa65c59816 RENAME TO idx_2c79cc7f65c59816');
		$this->addSql('ALTER INDEX IF EXISTS idx_220570fade12ab56 RENAME TO idx_2c79cc7fde12ab56');
		$this->addSql('ALTER INDEX IF EXISTS idx_220570fa8147be6d RENAME TO idx_2c79cc7f777abbab');
		$this->addSql('ALTER INDEX IF EXISTS idx_aa80376e65c59816 RENAME TO idx_24c07ba765c59816');
		$this->addSql('ALTER INDEX IF EXISTS idx_aa80376e85f30b8c RENAME TO idx_24c07ba785f30b8c');
	}
}
