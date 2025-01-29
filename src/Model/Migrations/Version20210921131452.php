<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210921131452 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE bl_call_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE bl_contact_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE bl_customer_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE bl_call (id BIGINT NOT NULL, bl_call_id BIGINT NOT NULL, bl_account_unique_id BIGINT NOT NULL, bl_is_crowd_client BOOLEAN NOT NULL, bl_interpreter_name_for_interpreter VARCHAR(255) DEFAULT NULL, bl_interpreter_referral_number VARCHAR(255) NOT NULL, bl_client_account_id BIGINT NOT NULL, bl_client_name VARCHAR(255) NOT NULL, bl_time_connected TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, bl_service_type_id INT NOT NULL, bl_communication_type_id INT NOT NULL, bl_duration VARCHAR(255) NOT NULL, bl_language_from_id INT NOT NULL, bl_language_to_id INT NOT NULL, bl_peer_rating_by_interpreter INT DEFAULT NULL, bl_call_quality_by_interpreter INT DEFAULT NULL, bl_invoice_amount DOUBLE PRECISION NOT NULL, bl_queue_time_seconds DOUBLE PRECISION NOT NULL, bl_toll_free_dialed BOOLEAN NOT NULL, bl_is_backstop_answered BOOLEAN NOT NULL, bl_client_company_id INT NOT NULL, bl_is_duration_update_pending BOOLEAN NOT NULL, bl_call_status VARCHAR(255) NOT NULL, bl_peer_rating_by_client INT NOT NULL, bl_call_quality_by_client INT NOT NULL, bl_from_number VARCHAR(255) DEFAULT NULL, bl_third_party VARCHAR(255) DEFAULT NULL, bl_third_party_duration VARCHAR(255) DEFAULT NULL, bl_operator_duration VARCHAR(255) DEFAULT NULL, bl_intake_duration VARCHAR(255) DEFAULT NULL, bl_interpreter_amount VARCHAR(255) DEFAULT NULL, bl_client_company_unique_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE TABLE bl_contact (id BIGINT NOT NULL, bl_contact_id BIGINT NOT NULL, bl_customer_id BIGINT DEFAULT NULL, bl_invitation_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bl_pin VARCHAR(255) DEFAULT NULL, bl_email VARCHAR(255) DEFAULT NULL, bl_phone VARCHAR(255) DEFAULT NULL, bl_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE TABLE bl_customer (id BIGINT NOT NULL, bl_customer_id BIGINT NOT NULL, bl_invited_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bl_accepted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, bl_status INT NOT NULL, bl_user_number INT NOT NULL, bl_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE SEQUENCE bl_communication_type_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE bl_languages_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE bl_service_type_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE SEQUENCE bl_translation_type_id_sequence INCREMENT BY 1 MINVALUE 1 START 1');
		$this->addSql('CREATE TABLE bl_communication_type (id BIGINT NOT NULL, id_communication_type BIGINT NOT NULL, bl_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_F31CC075302B9D52 ON bl_communication_type (id_communication_type)');
		$this->addSql('CREATE TABLE bl_language (id BIGINT NOT NULL, bl_id_language BIGINT NOT NULL, bl_enabled BOOLEAN DEFAULT NULL, bl_english_name VARCHAR(255) NOT NULL, bl_name VARCHAR(255) NOT NULL, bl_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_B4F65A908929B857 ON bl_language (bl_id_language)');
		$this->addSql('CREATE TABLE bl_service_type (id BIGINT NOT NULL, id_service_type BIGINT NOT NULL, bl_enabled BOOLEAN DEFAULT NULL, bl_name VARCHAR(255) NOT NULL, bl_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8CF12E7F615C96B7 ON bl_service_type (id_service_type)');
		$this->addSql('CREATE TABLE bl_translation_type (id BIGINT NOT NULL, bl_translation_communication_id BIGINT NOT NULL, id_translation_type BIGINT NOT NULL, bl_is_appointment_translation_type BOOLEAN DEFAULT NULL, bl_enabled BOOLEAN NOT NULL, bl_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_FFD0944331E41FB7 ON bl_translation_type (bl_translation_communication_id)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_FFD094434276A163 ON bl_translation_type (id_translation_type)');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT FK_FFD0944331E41FB7 FOREIGN KEY (bl_translation_communication_id) REFERENCES bl_communication_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN bl_customer_id TO customer_contact_id');
		$this->addSql('ALTER TABLE bl_contact ADD CONSTRAINT FK_D1C716A71A6821C8 FOREIGN KEY (customer_contact_id) REFERENCES bl_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('CREATE INDEX IDX_D1C716A71A6821C8 ON bl_contact (customer_contact_id)');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_E114A52C9EBCE684 ON bl_customer (id)');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE bl_call_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE bl_contact_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE bl_customer_id_sequence CASCADE');
		$this->addSql('DROP TABLE bl_call');
		$this->addSql('DROP TABLE bl_contact');
		$this->addSql('DROP TABLE bl_customer');
		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT FK_FFD0944331E41FB7');
		$this->addSql('DROP SEQUENCE bl_communication_type_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE bl_languages_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE bl_service_type_id_sequence CASCADE');
		$this->addSql('DROP SEQUENCE bl_translation_type_id_sequence CASCADE');
		$this->addSql('DROP TABLE bl_communication_type');
		$this->addSql('DROP TABLE bl_language');
		$this->addSql('DROP TABLE bl_service_type');
		$this->addSql('DROP TABLE bl_translation_type');
		$this->addSql('ALTER TABLE bl_contact DROP CONSTRAINT FK_D1C716A71A6821C8');
		$this->addSql('DROP INDEX IDX_D1C716A71A6821C8');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN customer_contact_id TO bl_customer_id');
		$this->addSql('DROP INDEX UNIQ_E114A52C9EBCE684');
	}
}
