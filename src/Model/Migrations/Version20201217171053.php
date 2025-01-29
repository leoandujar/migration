<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201217171053 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_marketing_email ALTER successful_delivery SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER opt_in_out SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER sent_count SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER open_count SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER delivered_count SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER bounce_count SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER unsubscriber_count SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER click_count SET DEFAULT 0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER open_ratio SET DEFAULT 0.0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER delivered_ratio SET DEFAULT 0.0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER bounce_ratio SET DEFAULT 0.0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER unsubscribed_ratio SET DEFAULT 0.0');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER click_ratio SET DEFAULT 0.0');
		$this->addSql('ALTER TABLE hs_marketing_email DROP publish_date');
		$this->addSql('ALTER TABLE hs_marketing_email DROP updated_date');
		$this->addSql('ALTER TABLE hs_marketing_email DROP created_date');
		$this->addSql('ALTER TABLE hs_marketing_email ADD publish_date TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER publish_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ADD updated_date TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER updated_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ADD created_date TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER created_date DROP DEFAULT');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE hs_marketing_email ALTER successful_delivery DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER opt_in_out DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER created_date TYPE BIGINT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER created_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER publish_date TYPE BIGINT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER publish_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER updated_date TYPE BIGINT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER updated_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER sent_count DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER open_count DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER delivered_count DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER bounce_count DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER unsubscriber_count DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER click_count DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER open_ratio DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER delivered_ratio DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER bounce_ratio DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER unsubscribed_ratio DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER click_ratio DROP DEFAULT');
	}
}
