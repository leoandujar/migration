<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018200118 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT FK_C118DCC08BF1FB99');
		$this->addSql('ALTER TABLE chart_group_assign ALTER chart_group TYPE UUID');
		$this->addSql('ALTER TABLE chart_group_assign ALTER chart_group DROP DEFAULT');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT FK_C118DCC08BF1FB99 FOREIGN KEY (chart_group) REFERENCES av_category_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE contact_person ADD category_groups JSON DEFAULT NULL');
		$this->addSql('ALTER TABLE customer ADD category_groups JSON DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE contact_person DROP category_groups');
		$this->addSql('ALTER TABLE customer DROP category_groups');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT fk_c118dcc08bf1fb99');
		$this->addSql('ALTER TABLE chart_group_assign ALTER chart_group TYPE UUID');
		$this->addSql('ALTER TABLE chart_group_assign ALTER chart_group DROP DEFAULT');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT fk_c118dcc08bf1fb99 FOREIGN KEY (chart_group) REFERENCES chart_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}
}
