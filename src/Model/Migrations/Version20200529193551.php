<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200529193551 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE qbo_payment_item_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('ALTER TABLE qbo_payment_item ADD remote_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('UPDATE qbo_payment_item qci set remote_id = qbo_payment_item_id WHERE 1=1;');
		$this->addSql('ALTER TABLE qbo_payment_item ALTER qbo_payment_item_id TYPE BIGINT USING (qbo_payment_item_id::bigint)');
		$this->addSql('ALTER TABLE qbo_payment_item ALTER qbo_payment_item_id DROP DEFAULT');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE qbo_payment_item_id_sequence CASCADE');
		$this->addSql('ALTER TABLE qbo_payment_item ALTER qbo_payment_item_id TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE qbo_payment_item ALTER qbo_payment_item_id DROP DEFAULT');
		$this->addSql('UPDATE qbo_payment_item qci set qbo_payment_item_id  = remote_id WHERE 1=1;');
		$this->addSql('ALTER TABLE qbo_payment_item DROP remote_id');
	}
}
