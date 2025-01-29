<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017021947 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE customer_rate_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE task_review_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE customer_rate (customer_rate_id BIGINT NOT NULL, activity_type_id BIGINT NOT NULL, calculation_unit_id BIGINT NOT NULL, tm_rates_id BIGINT DEFAULT NULL, customer_language_combination_id BIGINT NOT NULL, customer_price_profile_id BIGINT DEFAULT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version BIGINT NOT NULL, minimal_charge NUMERIC(16, 2) DEFAULT NULL, rate NUMERIC(19, 5) NOT NULL, customer_rate_notes TEXT DEFAULT NULL, PRIMARY KEY(customer_rate_id))');
		$this->addSql('CREATE INDEX IDX_8B7D9B4EC51EFA73 ON customer_rate (activity_type_id)');
		$this->addSql('CREATE INDEX IDX_8B7D9B4EFDE68D7D ON customer_rate (calculation_unit_id)');
		$this->addSql('CREATE INDEX IDX_8B7D9B4E4ED1E4FC ON customer_rate (tm_rates_id)');
		$this->addSql('CREATE INDEX IDX_8B7D9B4E8ADF6038 ON customer_rate (customer_language_combination_id)');
		$this->addSql('CREATE INDEX IDX_8B7D9B4E68D34FA0 ON customer_rate (customer_price_profile_id)');
		$this->addSql('CREATE TABLE task_review (task_review_id BIGINT NOT NULL, task_id BIGINT DEFAULT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version BIGINT NOT NULL, PRIMARY KEY(task_review_id))');
		$this->addSql('CREATE INDEX IDX_42EA05158DB60186 ON task_review (task_id)');
		$this->addSql('CREATE TABLE files_for_task_review (file_name VARCHAR(255) NOT NULL, task_review_id BIGINT NOT NULL, workflow_job_file_id BIGINT NOT NULL, PRIMARY KEY(task_review_id, file_name))');
		$this->addSql('CREATE INDEX IDX_FDE6BE4D41045BCA ON files_for_task_review (task_review_id)');
		$this->addSql('CREATE INDEX IDX_FDE6BE4D5F0A250A ON files_for_task_review (workflow_job_file_id)');
		$this->addSql('ALTER TABLE customer_rate ADD CONSTRAINT FK_8B7D9B4EC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_rate ADD CONSTRAINT FK_8B7D9B4EFDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_rate ADD CONSTRAINT FK_8B7D9B4E4ED1E4FC FOREIGN KEY (tm_rates_id) REFERENCES tm_rates (tm_rates_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_rate ADD CONSTRAINT FK_8B7D9B4E8ADF6038 FOREIGN KEY (customer_language_combination_id) REFERENCES customer_language_combination (customer_language_combination_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE customer_rate ADD CONSTRAINT FK_8B7D9B4E68D34FA0 FOREIGN KEY (customer_price_profile_id) REFERENCES customer_price_profile (customer_price_profile_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE task_review ADD CONSTRAINT FK_42EA05158DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE files_for_task_review ADD CONSTRAINT FK_FDE6BE4D41045BCA FOREIGN KEY (task_review_id) REFERENCES task_review (task_review_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE files_for_task_review ADD CONSTRAINT FK_FDE6BE4D5F0A250A FOREIGN KEY (workflow_job_file_id) REFERENCES workflow_job_file (workflow_job_file_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE files_for_task_review DROP CONSTRAINT FK_FDE6BE4D41045BCA');
		$this->addSql('DROP SEQUENCE customer_rate_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE task_review_id_sequence CASCADE');
		$this->addSql('DROP TABLE customer_rate');
		$this->addSql('DROP TABLE task_review');
		$this->addSql('DROP TABLE files_for_task_review');
	}
}
