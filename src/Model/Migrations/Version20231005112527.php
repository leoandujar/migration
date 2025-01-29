<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005112527 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'new custom fields for quotes';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE provider DROP evaluation_total_average');
		$this->addSql('ALTER TABLE quote ADD audience VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD domain VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD function VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD genre VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD purpose VARCHAR(2000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD rapid_fire VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD rush BOOLEAN DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD invoice_address VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD invoice_notes BOOLEAN DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD otn_number VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ADD pr_acc_status VARCHAR(1000) DEFAULT NULL');
		$this->addSql('ALTER TABLE quote ALTER send_source DROP DEFAULT');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE provider ADD evaluation_total_average TEXT DEFAULT NULL');
		$this->addSql('ALTER TABLE quote DROP audience');
		$this->addSql('ALTER TABLE quote DROP domain');
		$this->addSql('ALTER TABLE quote DROP function');
		$this->addSql('ALTER TABLE quote DROP genre');
		$this->addSql('ALTER TABLE quote DROP purpose');
		$this->addSql('ALTER TABLE quote DROP rapid_fire');
		$this->addSql('ALTER TABLE quote DROP rush');
		$this->addSql('ALTER TABLE quote DROP invoice_address');
		$this->addSql('ALTER TABLE quote DROP invoice_notes');
		$this->addSql('ALTER TABLE quote DROP otn_number');
		$this->addSql('ALTER TABLE quote DROP pr_acc_status');
		$this->addSql('ALTER TABLE quote ALTER send_source SET DEFAULT \'false\'');
	}
}
