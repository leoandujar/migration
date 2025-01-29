<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109040803 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5ab87636b9c');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5AB45DE1A76');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABF8512177');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABFE9B05DC');
		$this->addSql('DROP INDEX idx_a9fae5ab87636b9c');
		$this->addSql('ALTER TABLE bl_call ADD bl_communication_type_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD contact_unique_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD customer_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD queue_duration INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD status VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD peer_rating_by_customer INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD call_quality_by_customer INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call DROP bl_translation_type_id');
		$this->addSql('ALTER TABLE bl_call DROP account_unique_id');
		$this->addSql('ALTER TABLE bl_call DROP queue_time_seconds');
		$this->addSql('ALTER TABLE bl_call DROP client_name');
		$this->addSql('ALTER TABLE bl_call DROP call_status');
		$this->addSql('ALTER TABLE bl_call DROP peer_rating_by_client');
		$this->addSql('ALTER TABLE bl_call DROP call_quality_by_client');
		$this->addSql('ALTER TABLE bl_call ALTER duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER duration TYPE INT USING duration::integer');
		$this->addSql('ALTER TABLE bl_call ALTER third_party_duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER third_party_duration TYPE INT USING third_party_duration::integer');
		$this->addSql('ALTER TABLE bl_call ALTER operator_duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER operator_duration TYPE INT USING operator_duration::integer');
		$this->addSql('ALTER TABLE bl_call ALTER intake_duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER intake_duration TYPE INT USING intake_duration::integer');
		$this->addSql('ALTER TABLE bl_call ALTER interpreter_amount DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER interpreter_amount TYPE NUMERIC(19, 6) USING interpreter_amount::numeric(19,6)');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN time_connected TO start_date');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN invoice_amount TO customer_amount');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN client_company_unique_id TO customer_unique_id');
		$this->addSql('ALTER TABLE bl_language RENAME COLUMN bl_id_language TO bl_language_id');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_B4F65A908FD89F83 ON bl_language (bl_language_id)');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABEB80E479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (bl_communication_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB45DE1A76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABF8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (bl_service_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABFE9B05DC FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_A9FAE5ABEB80E479 ON bl_call (bl_communication_type_id)');

		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT fk_ffd0944342017efd');
		$this->addSql('DROP INDEX idx_ffd0944342017efd');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_communication_id TO bl_communication_type_id');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT FK_FFD09443EB80E479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (bl_communication_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_FFD09443EB80E479 ON bl_translation_type (bl_communication_type_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABEB80E479');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5abf8512177');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5abfe9b05dc');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5ab45de1a76');
		$this->addSql('DROP INDEX UNIQ_B4F65A908FD89F83');
		$this->addSql('ALTER TABLE bl_language RENAME COLUMN bl_language_id TO bl_id_language');
		$this->addSql('CREATE UNIQUE INDEX uniq_b4f65a908929b857 ON bl_language (bl_id_language)');
		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT FK_FFD09443EB80E479');
		$this->addSql('DROP INDEX IDX_FFD09443EB80E479');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_communication_type_id TO bl_communication_id');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT fk_ffd0944342017efd FOREIGN KEY (bl_communication_id) REFERENCES bl_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_ffd0944342017efd ON bl_translation_type (bl_communication_id)');
		$this->addSql('DROP INDEX IDX_A9FAE5ABEB80E479');
		$this->addSql('ALTER TABLE bl_call ADD bl_translation_type_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD account_unique_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD queue_time_seconds NUMERIC(19, 3) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD client_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD call_status VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD peer_rating_by_client INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD call_quality_by_client INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call DROP bl_communication_type_id');
		$this->addSql('ALTER TABLE bl_call DROP contact_unique_id');
		$this->addSql('ALTER TABLE bl_call DROP customer_name');
		$this->addSql('ALTER TABLE bl_call DROP queue_duration');
		$this->addSql('ALTER TABLE bl_call DROP status');
		$this->addSql('ALTER TABLE bl_call DROP peer_rating_by_customer');
		$this->addSql('ALTER TABLE bl_call DROP call_quality_by_customer');
		$this->addSql('ALTER TABLE bl_call ALTER duration TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE bl_call ALTER duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER third_party_duration TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE bl_call ALTER third_party_duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER operator_duration TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE bl_call ALTER operator_duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER intake_duration TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE bl_call ALTER intake_duration DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER interpreter_amount TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE bl_call ALTER interpreter_amount DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN start_date TO time_connected');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN customer_amount TO invoice_amount');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN customer_unique_id TO client_company_unique_id');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab87636b9c FOREIGN KEY (bl_translation_type_id) REFERENCES bl_translation_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abf8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abfe9b05dc FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab45de1a76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_a9fae5ab87636b9c ON bl_call (bl_translation_type_id)');
	}
}
