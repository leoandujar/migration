<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227213614 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'adding missing relations to av_flow';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_flow_step ADD next_action_id VARCHAR(36)');
		$this->addSql('ALTER TABLE av_flow_step ADD CONSTRAINT FK_E1F1ABAADAB82506 FOREIGN KEY (next_action_id) REFERENCES av_flow_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_flow ADD start_action_id VARCHAR(36) NOT NULL');
		$this->addSql('ALTER TABLE av_flow ADD CONSTRAINT FK_A411278555FA8BF3 FOREIGN KEY (start_action_id) REFERENCES av_flow_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A411278555FA8BF3 ON av_flow (start_action_id)');
		$this->addSql('ALTER TABLE av_flow_step RENAME TO av_flow_action');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->addSql('ALTER TABLE av_flow_action DROP CONSTRAINT FK_E1F1ABAADAB82506');
		$this->addSql('ALTER TABLE av_flow DROP CONSTRAINT FK_A411278555FA8BF3');
		$this->addSql('ALTER TABLE av_flow DROP start_action_id');
		$this->addSql('ALTER TABLE av_flow_action DROP next_action_id');
		$this->addSql('ALTER TABLE av_flow_action RENAME TO av_flow_step');
	}
}
