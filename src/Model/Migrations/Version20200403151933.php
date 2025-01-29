<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200403151933 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('ALTER TABLE task ADD margin NUMERIC(19, 5) DEFAULT NULL');
		$this->addSql('ALTER TABLE project DROP surcharge');
		$this->addSql('ALTER TABLE task_cat_charge ADD total_value NUMERIC(19, 5) DEFAULT NULL');
		$this->addSql('ALTER TABLE task_cat_charge ADD total_quantity INT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('ALTER TABLE task DROP margin');
		$this->addSql('ALTER TABLE project ADD surcharge INT DEFAULT NULL');
		$this->addSql('ALTER TABLE task_cat_charge DROP total_value');
		$this->addSql('ALTER TABLE task_cat_charge DROP total_quantity');
	}
}
