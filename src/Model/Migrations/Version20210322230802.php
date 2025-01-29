<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322230802 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
		$this->addSql('CREATE INDEX IDX_85115C1081C06096 ON analytics_project (activity_id)');
		$this->addSql('DROP INDEX IDX_85115C1081C06096');
		$this->addSql('ALTER TABLE cp_template ALTER data DROP DEFAULT');
		$this->addSql('ALTER TABLE custom_field_configuration ALTER preferences SET DEFAULT \'READ_WRITE::character varying\'');
		$this->addSql('ALTER TABLE provider_invoice ALTER vat_calculation_rule SET DEFAULT \'SUM_ITEMS::character varying\'');
		$this->addSql('ALTER TABLE tm_savings ALTER rounding_policy SET DEFAULT \'ROUND_LAST::text\'');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SCHEMA public');
		$this->addSql('CREATE INDEX IDX_85115C1081C06096 ON analytics_project (activity_id)');
		$this->addSql('ALTER TABLE cp_template ALTER data SET DEFAULT \'{}\'');
		$this->addSql('ALTER TABLE custom_field_configuration ALTER preferences SET DEFAULT \'READ_WRITE\'');
		$this->addSql('ALTER TABLE provider_invoice ALTER vat_calculation_rule SET DEFAULT \'SUM_ITEMS\'');
		$this->addSql('ALTER TABLE tm_savings ALTER rounding_policy SET DEFAULT \'ROUND_LAST\'');
	}
}
