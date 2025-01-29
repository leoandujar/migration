<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106211830 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'added relation between av_user and xtrf_user';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_user ADD xtrf_user_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE av_user ADD CONSTRAINT FK_7B4227BCE7927C74 FOREIGN KEY (xtrf_user_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_7B4227BCE7927C74 ON av_user (xtrf_user_id)');
		$this->addSql('CREATE INDEX IDX_36A696037CA9501E ON xtrf_user (xtrf_user_id)');
		$this->addSql('CREATE INDEX IDX_36A69603E7927C74 ON xtrf_user (xtrf_login)');
		$this->addSql('ALTER TABLE av_workflow_monitor ADD CONSTRAINT FK_2C79CC7F65C59816 FOREIGN KEY (workflow) REFERENCES av_workflow (wf_workflow_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE av_workflow_monitor DROP CONSTRAINT FK_2C79CC7F65C59816');
		$this->addSql('ALTER TABLE av_user DROP CONSTRAINT FK_7B4227BCE7927C74');
		$this->addSql('DROP INDEX IDX_7B4227BCE7927C74');
		$this->addSql('DROP INDEX IDX_36A696037CA9501E');
		$this->addSql('DROP INDEX IDX_36A69603E7927C74');
		$this->addSql('ALTER TABLE av_user DROP xtrf_user_id');
	}
}
