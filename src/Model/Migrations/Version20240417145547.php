<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417145547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add XtrfCpConfiguration entity and related tables';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE rest_api_configuration (rest_api_configuration_id BIGINT NOT NULL, last_modification_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, version INT NOT NULL, all_active_languages_available BOOLEAN NOT NULL, all_active_specializations_available BOOLEAN NOT NULL, contact_email VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, portal_url VARCHAR(255) NOT NULL, landing_card_enabled BOOLEAN DEFAULT \'false\' NOT NULL, landing_card_title TEXT DEFAULT NULL, landing_card_content TEXT DEFAULT NULL, all_active_services_available BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(rest_api_configuration_id))');
        $this->addSql('CREATE TABLE rest_api_languages (rest_api_configuration_id BIGINT NOT NULL, language_id BIGINT NOT NULL, PRIMARY KEY(rest_api_configuration_id, language_id))');
        $this->addSql('CREATE INDEX IDX_F58CFCCB10032480 ON rest_api_languages (rest_api_configuration_id)');
        $this->addSql('CREATE INDEX IDX_F58CFCCB82F1BAF4 ON rest_api_languages (language_id)');
        $this->addSql('CREATE TABLE rest_api_services (rest_api_configuration_id BIGINT NOT NULL, service_id BIGINT NOT NULL, PRIMARY KEY(rest_api_configuration_id, service_id))');
        $this->addSql('CREATE INDEX IDX_2BBB7CDE10032480 ON rest_api_services (rest_api_configuration_id)');
        $this->addSql('CREATE INDEX IDX_2BBB7CDEED5CA9E6 ON rest_api_services (service_id)');
        $this->addSql('CREATE TABLE rest_api_specializations (rest_api_configuration_id BIGINT NOT NULL, specialization_id BIGINT NOT NULL, PRIMARY KEY(rest_api_configuration_id, language_specialization_id))');
        $this->addSql('CREATE INDEX IDX_40921FA210032480 ON rest_api_specializations (rest_api_configuration_id)');
        $this->addSql('CREATE INDEX IDX_40921FA2FA846217 ON rest_api_specializations (language_specialization_id)');
        $this->addSql('ALTER TABLE rest_api_languages ADD CONSTRAINT FK_F58CFCCB10032480 FOREIGN KEY (rest_api_configuration_id) REFERENCES rest_api_configuration (rest_api_configuration_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rest_api_languages ADD CONSTRAINT FK_F58CFCCB82F1BAF4 FOREIGN KEY (language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rest_api_services ADD CONSTRAINT FK_2BBB7CDE10032480 FOREIGN KEY (rest_api_configuration_id) REFERENCES rest_api_configuration (rest_api_configuration_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rest_api_services ADD CONSTRAINT FK_2BBB7CDEED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (service_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rest_api_specializations ADD CONSTRAINT FK_40921FA210032480 FOREIGN KEY (rest_api_configuration_id) REFERENCES rest_api_configuration (rest_api_configuration_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rest_api_specializations ADD CONSTRAINT FK_40921FA2FA846217 FOREIGN KEY (language_specialization_id) REFERENCES language_specialization (language_specialization_id) NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE rest_api_languages DROP CONSTRAINT FK_F58CFCCB10032480');
        $this->addSql('ALTER TABLE rest_api_services DROP CONSTRAINT FK_2BBB7CDE10032480');
        $this->addSql('ALTER TABLE rest_api_specializations DROP CONSTRAINT FK_40921FA210032480');
        $this->addSql('DROP TABLE rest_api_configuration');
        $this->addSql('DROP TABLE rest_api_languages');
        $this->addSql('DROP TABLE rest_api_services');
        $this->addSql('DROP TABLE rest_api_specializations');

    }
}
