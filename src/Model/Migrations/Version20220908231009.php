<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220908231009 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE chart RENAME TO av_chart');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT FK_C118DCC0E5562A2A');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT FK_C118DCC0E5562A2A FOREIGN KEY (chart) REFERENCES av_chart (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT fk_c118dcc0e5562a2b');
		$this->addSql('ALTER TABLE chart_group_assign DROP CONSTRAINT FK_C118DCC0E5562A2A');
		$this->addSql('ALTER TABLE chart_group_assign ADD CONSTRAINT FK_C118DCC0E5562A2A FOREIGN KEY (chart) REFERENCES chart (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE av_chart RENAME TO chart');
	}
}
