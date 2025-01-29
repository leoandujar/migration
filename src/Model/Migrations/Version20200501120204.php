<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200501120204 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE qbo_account (id VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, sub_account BOOLEAN DEFAULT NULL, parent_ref VARCHAR(255) DEFAULT NULL, fully_qualified_name VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT NULL, classification VARCHAR(255) DEFAULT NULL, account_type VARCHAR(255) DEFAULT NULL, account_sub_type VARCHAR(255) DEFAULT NULL, current_balance NUMERIC(19, 2) DEFAULT NULL, current_balance_with_sub_accounts NUMERIC(19, 2) DEFAULT NULL, currency_ref VARCHAR(255) DEFAULT NULL, metadata_created_by_ref TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_create_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_modified_by_ref TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_updated_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_last_changed_in_qb TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata_synchronized BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_6EBFCE57BF396750 ON qbo_account (id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE qbo_account');
	}
}
