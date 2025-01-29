<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108114551 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DELETE FROM bl_call WHERE 1=1');
		$this->addSql('ALTER TABLE bl_call ADD bl_translation_type_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_source_language_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_target_language_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_customer_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call DROP bl_communication_type_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_language_from_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_language_to_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_client_company_id');
		$this->addSql('ALTER TABLE bl_call ALTER bl_service_type_id TYPE BIGINT');
		$this->addSql('ALTER TABLE bl_call ALTER bl_service_type_id DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER bl_invoice_amount TYPE NUMERIC(19, 6)');
		$this->addSql('ALTER TABLE bl_call ALTER bl_invoice_amount DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER bl_queue_time_seconds TYPE NUMERIC(19, 3)');
		$this->addSql('ALTER TABLE bl_call ALTER bl_queue_time_seconds DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_client_account_id TO bl_contact_id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_interpreter_name_for_interpreter TO interpreter_name');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABA3EE6CC0 FOREIGN KEY (bl_contact_id) REFERENCES bl_contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABF8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB87636B9C FOREIGN KEY (bl_translation_type_id) REFERENCES bl_translation_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABFE9B05DC FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB45DE1A76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB9EBCE684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_A9FAE5ABA3EE6CC0 ON bl_call (bl_contact_id)');
		$this->addSql('CREATE INDEX IDX_A9FAE5ABF8512177 ON bl_call (bl_service_type_id)');
		$this->addSql('CREATE INDEX IDX_A9FAE5AB87636B9C ON bl_call (bl_translation_type_id)');
		$this->addSql('CREATE INDEX IDX_A9FAE5ABFE9B05DC ON bl_call (bl_source_language_id)');
		$this->addSql('CREATE INDEX IDX_A9FAE5AB45DE1A76 ON bl_call (bl_target_language_id)');
		$this->addSql('CREATE INDEX IDX_A9FAE5AB9EBCE684 ON bl_call (bl_customer_id)');
		$this->addSql('ALTER TABLE bl_contact DROP CONSTRAINT fk_d1c716a71a6821c8');
		$this->addSql('DROP INDEX idx_d1c716a71a6821c8');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN customer_contact_id TO bl_customer_id');
		$this->addSql('ALTER TABLE bl_contact ADD CONSTRAINT FK_D1C716A79EBCE684 FOREIGN KEY (bl_customer_id) REFERENCES bl_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_D1C716A79EBCE684 ON bl_contact (bl_customer_id)');
		$this->addSql('DROP INDEX UNIQ_E114A52C9EBCE684');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E114A52C9EBCE684 ON bl_customer (bl_customer_id)');
		$this->addSql('ALTER TABLE bl_language ADD xtrf_language_id BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_language ADD CONSTRAINT FK_B4F65A90CE6064C2 FOREIGN KEY (xtrf_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_B4F65A90CE6064C2 ON bl_language (xtrf_language_id)');
		$this->addSql('DROP INDEX uniq_8cf12e7f615c96b7');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN id_service_type TO bl_service_type_id');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8CF12E7FF8512177 ON bl_service_type (bl_service_type_id)');
		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT fk_ffd0944331e41fb7');
		$this->addSql('DROP INDEX uniq_ffd094434276a163');
		$this->addSql('DROP INDEX idx_ffd0944331e41fb7');
		$this->addSql('DELETE FROM bl_translation_type WHERE 1=1');
		$this->addSql('ALTER TABLE bl_translation_type ADD bl_communication_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_translation_type ADD bl_translation_type_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_translation_type DROP bl_translation_communication_id');
		$this->addSql('ALTER TABLE bl_translation_type DROP id_translation_type');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT FK_FFD0944342017EFD FOREIGN KEY (bl_communication_id) REFERENCES bl_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_FFD0944342017EFD ON bl_translation_type (bl_communication_id)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_FFD0944387636B9C ON bl_translation_type (bl_translation_type_id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABA3EE6CC0');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABF8512177');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5AB87636B9C');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABFE9B05DC');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5AB45DE1A76');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5AB9EBCE684');
		$this->addSql('DROP INDEX IDX_A9FAE5ABA3EE6CC0');
		$this->addSql('DROP INDEX IDX_A9FAE5ABF8512177');
		$this->addSql('DROP INDEX IDX_A9FAE5AB87636B9C');
		$this->addSql('DROP INDEX IDX_A9FAE5ABFE9B05DC');
		$this->addSql('DROP INDEX IDX_A9FAE5AB45DE1A76');
		$this->addSql('DROP INDEX IDX_A9FAE5AB9EBCE684');
		$this->addSql('ALTER TABLE bl_call ADD bl_client_account_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_communication_type_id INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_language_from_id INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_language_to_id INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_client_company_id INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call DROP bl_contact_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_translation_type_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_source_language_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_target_language_id');
		$this->addSql('ALTER TABLE bl_call DROP bl_customer_id');
		$this->addSql('ALTER TABLE bl_call ALTER bl_service_type_id TYPE INT');
		$this->addSql('ALTER TABLE bl_call ALTER bl_service_type_id DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER bl_invoice_amount TYPE DOUBLE PRECISION');
		$this->addSql('ALTER TABLE bl_call ALTER bl_invoice_amount DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call ALTER bl_queue_time_seconds TYPE DOUBLE PRECISION');
		$this->addSql('ALTER TABLE bl_call ALTER bl_queue_time_seconds DROP DEFAULT');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN interpreter_name TO bl_interpreter_name_for_interpreter');
		$this->addSql('ALTER TABLE bl_language DROP CONSTRAINT FK_B4F65A90CE6064C2');
		$this->addSql('DROP INDEX IDX_B4F65A90CE6064C2');
		$this->addSql('ALTER TABLE bl_language DROP xtrf_language_id');
		$this->addSql('DROP INDEX UNIQ_8CF12E7FF8512177');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN bl_service_type_id TO id_service_type');
		$this->addSql('CREATE UNIQUE INDEX uniq_8cf12e7f615c96b7 ON bl_service_type (id_service_type)');
		$this->addSql('DROP INDEX uniq_e114a52c9ebce684');
		$this->addSql('CREATE UNIQUE INDEX uniq_e114a52c9ebce684 ON bl_customer (id)');
		$this->addSql('ALTER TABLE bl_contact DROP CONSTRAINT FK_D1C716A79EBCE684');
		$this->addSql('DROP INDEX IDX_D1C716A79EBCE684');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN bl_customer_id TO customer_contact_id');
		$this->addSql('ALTER TABLE bl_contact ADD CONSTRAINT fk_d1c716a71a6821c8 FOREIGN KEY (customer_contact_id) REFERENCES bl_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX idx_d1c716a71a6821c8 ON bl_contact (customer_contact_id)');
		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT FK_FFD0944342017EFD');
		$this->addSql('DROP INDEX IDX_FFD0944342017EFD');
		$this->addSql('DROP INDEX UNIQ_FFD0944387636B9C');
		$this->addSql('ALTER TABLE bl_translation_type ADD bl_translation_communication_id BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_translation_type ADD id_translation_type BIGINT NOT NULL');
		$this->addSql('ALTER TABLE bl_translation_type DROP bl_communication_id');
		$this->addSql('ALTER TABLE bl_translation_type DROP bl_translation_type_id');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT fk_ffd0944331e41fb7 FOREIGN KEY (bl_translation_communication_id) REFERENCES bl_communication_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE UNIQUE INDEX uniq_ffd094434276a163 ON bl_translation_type (id_translation_type)');
		$this->addSql('CREATE INDEX idx_ffd0944331e41fb7 ON bl_translation_type (bl_translation_communication_id)');
	}
}
