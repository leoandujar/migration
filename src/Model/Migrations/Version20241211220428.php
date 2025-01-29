<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211220428 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Migration added for the relationship between hsdeals and hscustomer';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		//$this->addSql('ALTER TABLE hs_deal ADD hs_customer_id BIGINT DEFAULT NULL');
		//$this->addSql('ALTER TABLE hs_deal ADD CONSTRAINT FK_D07AE483DFFC4FF0 FOREIGN KEY (hs_customer_id) REFERENCES hs_customer (hs_customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		//$this->addSql('CREATE INDEX IDX_D07AE483DFFC4FF0 ON hs_deal (hs_customer_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hs_deal DROP CONSTRAINT FK_D07AE483DFFC4FF0');
		$this->addSql('DROP INDEX IDX_D07AE483DFFC4FF0');
		$this->addSql('ALTER TABLE hs_deal DROP hs_customer_id');
	}
}
