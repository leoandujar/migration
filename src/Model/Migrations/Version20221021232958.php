<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221021232958 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task_cat_charge ALTER old_rate_origin DROP NOT NULL');
		$this->addSql('ALTER TABLE task_charge ALTER old_rate DROP NOT NULL');
		$this->addSql('ALTER TABLE task_charge ALTER old_quantity DROP NOT NULL');
		$this->addSql('ALTER TABLE task_charge ALTER old_rate_origin DROP NOT NULL');
		$this->addSql('ALTER TABLE tm_savings ALTER old_base_rate DROP NOT NULL');
		$this->addSql('ALTER TABLE tm_savings_item ALTER old_quantity DROP NOT NULL');
		$this->addSql('ALTER TABLE tm_savings_item ALTER old_fixed_rate DROP NOT NULL');
		$this->addSql('ALTER TABLE tm_savings_item ALTER old_percentage_rate DROP NOT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE task_cat_charge ALTER old_rate_origin SET NOT NULL');
		$this->addSql('ALTER TABLE task_charge ALTER old_rate SET NOT NULL');
		$this->addSql('ALTER TABLE task_charge ALTER old_rate_origin SET NOT NULL');
		$this->addSql('ALTER TABLE task_charge ALTER old_quantity SET NOT NULL');
		$this->addSql('ALTER TABLE tm_savings ALTER old_base_rate SET NOT NULL');
		$this->addSql('ALTER TABLE tm_savings_item ALTER old_quantity SET NOT NULL');
		$this->addSql('ALTER TABLE tm_savings_item ALTER old_fixed_rate SET NOT NULL');
		$this->addSql('ALTER TABLE tm_savings_item ALTER old_percentage_rate SET NOT NULL');
	}
}
