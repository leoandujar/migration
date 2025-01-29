<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108122229 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_call ADD is_crowd_client BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD interpreter_referral_number VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD client_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD duration VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD peer_rating_by_interpreter INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD call_quality_by_interpreter INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD toll_free_dialed BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD is_backstop_answered BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD is_duration_update_pending BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD call_status VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD peer_rating_by_client INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD call_quality_by_client INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD from_number VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD third_party VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD third_party_duration VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD operator_duration VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD intake_duration VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD interpreter_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD client_company_unique_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call DROP bl_is_crowd_client');
		$this->addSql('ALTER TABLE bl_call DROP bl_interpreter_referral_number');
		$this->addSql('ALTER TABLE bl_call DROP bl_client_name');
		$this->addSql('ALTER TABLE bl_call DROP bl_duration');
		$this->addSql('ALTER TABLE bl_call DROP bl_peer_rating_by_interpreter');
		$this->addSql('ALTER TABLE bl_call DROP bl_call_quality_by_interpreter');
		$this->addSql('ALTER TABLE bl_call DROP bl_toll_free_dialed');
		$this->addSql('ALTER TABLE bl_call DROP bl_is_backstop_answered');
		$this->addSql('ALTER TABLE bl_call DROP bl_is_duration_update_pending');
		$this->addSql('ALTER TABLE bl_call DROP bl_call_status');
		$this->addSql('ALTER TABLE bl_call DROP bl_peer_rating_by_client');
		$this->addSql('ALTER TABLE bl_call DROP bl_call_quality_by_client');
		$this->addSql('ALTER TABLE bl_call DROP bl_from_number');
		$this->addSql('ALTER TABLE bl_call DROP bl_third_party');
		$this->addSql('ALTER TABLE bl_call DROP bl_third_party_duration');
		$this->addSql('ALTER TABLE bl_call DROP bl_operator_duration');
		$this->addSql('ALTER TABLE bl_call DROP bl_intake_duration');
		$this->addSql('ALTER TABLE bl_call DROP bl_interpreter_amount');
		$this->addSql('ALTER TABLE bl_call DROP bl_client_company_unique_id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_account_unique_id TO account_unique_id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_time_connected TO time_connected');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_invoice_amount TO invoice_amount');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN bl_queue_time_seconds TO queue_time_seconds');
		$this->addSql('DROP INDEX uniq_f31cc075302b9d52');
		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN id_communication_type TO bl_communication_type_id');
		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN bl_name TO name');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_F31CC075EB80E479 ON bl_communication_type (bl_communication_type_id)');
		$this->addSql('ALTER TABLE bl_contact ADD pin VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact ADD email VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact ADD phone VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact ADD name VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact DROP bl_pin');
		$this->addSql('ALTER TABLE bl_contact DROP bl_email');
		$this->addSql('ALTER TABLE bl_contact DROP bl_phone');
		$this->addSql('ALTER TABLE bl_contact DROP bl_name');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN bl_invitation_date TO invitation_date');
		$this->addSql('ALTER TABLE bl_customer ADD invited_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_customer ADD accepted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('DELETE FROM bl_customer WHERE 1=1');
		$this->addSql('ALTER TABLE bl_customer ADD status INT NOT NULL');
		$this->addSql('ALTER TABLE bl_customer ADD user_number INT NOT NULL');
		$this->addSql('ALTER TABLE bl_customer DROP bl_invited_date');
		$this->addSql('ALTER TABLE bl_customer DROP bl_accepted_date');
		$this->addSql('ALTER TABLE bl_customer DROP bl_status');
		$this->addSql('ALTER TABLE bl_customer DROP bl_user_number');
		$this->addSql('ALTER TABLE bl_customer RENAME COLUMN bl_name TO name');
		$this->addSql('DELETE FROM bl_language WHERE 1=1');
		$this->addSql('ALTER TABLE bl_language ADD english_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_language ADD name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_language ADD code VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_language DROP bl_english_name');
		$this->addSql('ALTER TABLE bl_language DROP bl_name');
		$this->addSql('ALTER TABLE bl_language DROP bl_code');
		$this->addSql('ALTER TABLE bl_language RENAME COLUMN bl_enabled TO enabled');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN bl_enabled TO enabled');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN bl_name TO name');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_is_appointment_translation_type TO is_appointment_translation_type');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_enabled TO enabled');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN bl_name TO name');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_language ADD bl_english_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_language ADD bl_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_language ADD bl_code VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_language DROP english_name');
		$this->addSql('ALTER TABLE bl_language DROP name');
		$this->addSql('ALTER TABLE bl_language DROP code');
		$this->addSql('ALTER TABLE bl_language RENAME COLUMN enabled TO bl_enabled');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN enabled TO bl_enabled');
		$this->addSql('ALTER TABLE bl_service_type RENAME COLUMN name TO bl_name');
		$this->addSql('ALTER TABLE bl_customer ADD bl_invited_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_customer ADD bl_accepted_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_customer ADD bl_status INT NOT NULL');
		$this->addSql('ALTER TABLE bl_customer ADD bl_user_number INT NOT NULL');
		$this->addSql('ALTER TABLE bl_customer DROP invited_date');
		$this->addSql('ALTER TABLE bl_customer DROP accepted_date');
		$this->addSql('ALTER TABLE bl_customer DROP status');
		$this->addSql('ALTER TABLE bl_customer DROP user_number');
		$this->addSql('ALTER TABLE bl_customer RENAME COLUMN name TO bl_name');
		$this->addSql('ALTER TABLE bl_contact ADD bl_pin VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact ADD bl_email VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact ADD bl_phone VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact ADD bl_name VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_contact DROP pin');
		$this->addSql('ALTER TABLE bl_contact DROP email');
		$this->addSql('ALTER TABLE bl_contact DROP phone');
		$this->addSql('ALTER TABLE bl_contact DROP name');
		$this->addSql('ALTER TABLE bl_contact RENAME COLUMN invitation_date TO bl_invitation_date');
		$this->addSql('DROP INDEX UNIQ_F31CC075EB80E479');
		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN bl_communication_type_id TO id_communication_type');
		$this->addSql('ALTER TABLE bl_communication_type RENAME COLUMN name TO bl_name');
		$this->addSql('CREATE UNIQUE INDEX uniq_f31cc075302b9d52 ON bl_communication_type (id_communication_type)');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN is_appointment_translation_type TO bl_is_appointment_translation_type');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN enabled TO bl_enabled');
		$this->addSql('ALTER TABLE bl_translation_type RENAME COLUMN name TO bl_name');
		$this->addSql('ALTER TABLE bl_call ADD bl_is_crowd_client BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_interpreter_referral_number VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_client_name VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_duration VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_peer_rating_by_interpreter INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_call_quality_by_interpreter INT DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_toll_free_dialed BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_is_backstop_answered BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_is_duration_update_pending BOOLEAN NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_call_status VARCHAR(255) NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_peer_rating_by_client INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_call_quality_by_client INT NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_from_number VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_third_party VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_third_party_duration VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_operator_duration VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_intake_duration VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_interpreter_amount VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call ADD bl_client_company_unique_id VARCHAR(255) DEFAULT NULL');
		$this->addSql('ALTER TABLE bl_call DROP is_crowd_client');
		$this->addSql('ALTER TABLE bl_call DROP interpreter_referral_number');
		$this->addSql('ALTER TABLE bl_call DROP client_name');
		$this->addSql('ALTER TABLE bl_call DROP duration');
		$this->addSql('ALTER TABLE bl_call DROP peer_rating_by_interpreter');
		$this->addSql('ALTER TABLE bl_call DROP call_quality_by_interpreter');
		$this->addSql('ALTER TABLE bl_call DROP toll_free_dialed');
		$this->addSql('ALTER TABLE bl_call DROP is_backstop_answered');
		$this->addSql('ALTER TABLE bl_call DROP is_duration_update_pending');
		$this->addSql('ALTER TABLE bl_call DROP call_status');
		$this->addSql('ALTER TABLE bl_call DROP peer_rating_by_client');
		$this->addSql('ALTER TABLE bl_call DROP call_quality_by_client');
		$this->addSql('ALTER TABLE bl_call DROP from_number');
		$this->addSql('ALTER TABLE bl_call DROP third_party');
		$this->addSql('ALTER TABLE bl_call DROP third_party_duration');
		$this->addSql('ALTER TABLE bl_call DROP operator_duration');
		$this->addSql('ALTER TABLE bl_call DROP intake_duration');
		$this->addSql('ALTER TABLE bl_call DROP interpreter_amount');
		$this->addSql('ALTER TABLE bl_call DROP client_company_unique_id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN account_unique_id TO bl_account_unique_id');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN time_connected TO bl_time_connected');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN invoice_amount TO bl_invoice_amount');
		$this->addSql('ALTER TABLE bl_call RENAME COLUMN queue_time_seconds TO bl_queue_time_seconds');
	}
}
