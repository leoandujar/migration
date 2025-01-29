<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221101202525 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE av_workflow_category_group (workflow BIGINT NOT NULL, category_group UUID NOT NULL, PRIMARY KEY(workflow, category_group))');
		$this->addSql('CREATE INDEX IDX_24C07BA765C59816 ON av_workflow_category_group (workflow)');
		$this->addSql('CREATE INDEX IDX_24C07BA785F30B8C ON av_workflow_category_group (category_group)');
		$this->addSql('ALTER TABLE av_workflow_category_group ADD CONSTRAINT FK_24C07BA765C59816 FOREIGN KEY (workflow) REFERENCES av_workflow (wf_workflow_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_workflow_category_group ADD CONSTRAINT FK_24C07BA785F30B8C FOREIGN KEY (category_group) REFERENCES av_category_group (av_category_group_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_user ADD category_groups JSON DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE av_workflow_category_group');
		$this->addSql('ALTER TABLE av_user DROP category_groups');
	}
}
