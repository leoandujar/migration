<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313061815 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'To delete the cp_setting_rule table and its foreign key from the cp_setting table.';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE cp_setting DROP CONSTRAINT FK_A7E7925CB39E0B0C');
		$this->addSql('DROP SEQUENCE cp_setting_rule_id_sequence CASCADE');
		$this->addSql('ALTER TABLE cp_setting_rule DROP CONSTRAINT FK_BCBC3B392C7C2CBA');
		$this->addSql('DROP TABLE cp_setting_rule');
		$this->addSql('DROP INDEX UNIQ_A7E7925CB39E0B0C');
		$this->addSql('ALTER TABLE cp_setting DROP cp_setting_rule_id');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('CREATE SEQUENCE cp_setting_rule_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE cp_setting_rule (cp_setting_rule_id BIGINT NOT NULL, workflow_id BIGINT DEFAULT NULL, name VARCHAR(50) NOT NULL, event VARCHAR(50) NOT NULL, type VARCHAR(30) NOT NULL, filters JSONB DEFAULT NULL, parameters JSONB DEFAULT NULL, PRIMARY KEY(cp_setting_rule_id))');
		$this->addSql('CREATE INDEX IDX_BCBC3B392C7C2CBA ON cp_setting_rule (workflow_id)');
		$this->addSql('ALTER TABLE cp_setting_rule ADD CONSTRAINT FK_BCBC3B392C7C2CBA FOREIGN KEY (workflow_id) REFERENCES av_workflow (wf_workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE cp_setting ADD cp_setting_rule_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE cp_setting ADD CONSTRAINT FK_A7E7925CB39E0B0C FOREIGN KEY (cp_setting_rule_id) REFERENCES cp_setting_rule (cp_setting_rule_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_A7E7925CB39E0B0C ON cp_setting (cp_setting_rule_id)');
	}
}
