<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017210307 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Add a many-to-many relationship between av_flow and av_category_group.';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE av_flow_category_group (flow_id VARCHAR(36) NOT NULL, category_group_id UUID NOT NULL, PRIMARY KEY(flow_id, category_group_id))');
		$this->addSql('CREATE INDEX IDX_9E423A137EB60D1B ON av_flow_category_group (flow_id)');
		$this->addSql('CREATE INDEX IDX_9E423A13492E5D3C ON av_flow_category_group (category_group_id)');
		$this->addSql('ALTER TABLE av_flow_category_group ADD CONSTRAINT FK_9E423A137EB60D1B FOREIGN KEY (flow_id) REFERENCES av_flow (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_flow_category_group ADD CONSTRAINT FK_9E423A13492E5D3C FOREIGN KEY (category_group_id) REFERENCES av_category_group (av_category_group_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE av_flow_category_group DROP CONSTRAINT FK_9E423A137EB60D1B');
		$this->addSql('ALTER TABLE av_flow_category_group DROP CONSTRAINT FK_9E423A13492E5D3C');
		$this->addSql('DROP TABLE av_flow_category_group');
	}
}
