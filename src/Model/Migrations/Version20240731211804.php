<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240731211804 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add a self-reference to the av_flow_step table.';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_flow_step ADD parent_id VARCHAR(36) DEFAULT NULL');
		$this->addSql('ALTER TABLE av_flow_step ADD CONSTRAINT FK_8DF096E8727ACA70 FOREIGN KEY (parent_id) REFERENCES av_flow_step (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_8DF096E8727ACA70 ON av_flow_step (parent_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_flow_step DROP CONSTRAINT FK_8DF096E8727ACA70');
		$this->addSql('ALTER TABLE av_flow_step DROP parent_id');
	}
}
