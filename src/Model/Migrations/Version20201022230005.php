<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201022230005 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE customer_price_list_rate_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE customer_price_list_language_combination_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE customer_price_list_rate (customer_price_list_rate_id BIGINT NOT NULL, activity_type_id BIGINT NOT NULL, calculation_unit_id BIGINT NOT NULL, tm_rates_id BIGINT DEFAULT NULL, customer_price_list_language_combination_id BIGINT NOT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version BIGINT NOT NULL, minimal_charge NUMERIC(16, 2) DEFAULT NULL, rate NUMERIC(19, 5) NOT NULL, PRIMARY KEY(customer_price_list_rate_id))');
		$this->addSql('CREATE INDEX IDX_62BB95B6C51EFA73 ON customer_price_list_rate (activity_type_id)');
		$this->addSql('CREATE INDEX IDX_62BB95B6FDE68D7D ON customer_price_list_rate (calculation_unit_id)');
		$this->addSql('CREATE INDEX IDX_62BB95B64ED1E4FC ON customer_price_list_rate (tm_rates_id)');
		$this->addSql('CREATE INDEX IDX_62BB95B680B561EB ON customer_price_list_rate (customer_price_list_language_combination_id)');
		$this->addSql('CREATE TABLE customer_price_list_language_combination (customer_price_list_language_combination_id BIGINT NOT NULL, source_language_id BIGINT DEFAULT NULL, target_language_id BIGINT DEFAULT NULL, customer_price_list_id BIGINT NOT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version INT NOT NULL, minimal_charge NUMERIC(16, 2) DEFAULT NULL, PRIMARY KEY(customer_price_list_language_combination_id))');
		$this->addSql('CREATE INDEX IDX_6CEC9FFBBE8EEA54 ON customer_price_list_language_combination (source_language_id)');
		$this->addSql('CREATE INDEX IDX_6CEC9FFB5CBF5FE ON customer_price_list_language_combination (target_language_id)');
		$this->addSql('CREATE INDEX IDX_6CEC9FFB32EDAED4 ON customer_price_list_language_combination (customer_price_list_id)');
		$this->addSql('ALTER TABLE customer_price_list_rate ADD CONSTRAINT FK_62BB95B6C51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_price_list_rate ADD CONSTRAINT FK_62BB95B6FDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_price_list_rate ADD CONSTRAINT FK_62BB95B64ED1E4FC FOREIGN KEY (tm_rates_id) REFERENCES tm_rates (tm_rates_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_price_list_rate ADD CONSTRAINT FK_62BB95B680B561EB FOREIGN KEY (customer_price_list_language_combination_id) REFERENCES customer_price_list_language_combination (customer_price_list_language_combination_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_price_list_language_combination ADD CONSTRAINT FK_6CEC9FFBBE8EEA54 FOREIGN KEY (source_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_price_list_language_combination ADD CONSTRAINT FK_6CEC9FFB5CBF5FE FOREIGN KEY (target_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_price_list_language_combination ADD CONSTRAINT FK_6CEC9FFB32EDAED4 FOREIGN KEY (customer_price_list_id) REFERENCES customer_price_list (customer_price_list_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE customer_price_list_rate DROP CONSTRAINT FK_62BB95B680B561EB');
		$this->addSql('DROP SEQUENCE customer_price_list_rate_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE customer_price_list_language_combination_id_sequence CASCADE');
		$this->addSql('DROP TABLE customer_price_list_rate');
		$this->addSql('DROP TABLE customer_price_list_language_combination');
	}
}
