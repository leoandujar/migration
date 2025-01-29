<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211110134259 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5AB45DE1A76');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABEB80E479');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABF8512177');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT FK_A9FAE5ABFE9B05DC');
		$this->addSql('ALTER TABLE bl_call ALTER bl_contact_id SET NOT NULL');
		$this->addSql('CREATE UNIQUE INDEX UNIQ_8CF12E7FF8512178 ON bl_service_type (id)');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5AB45DE1A76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABEB80E479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABF8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT FK_A9FAE5ABFE9B05DC FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT FK_FFD09443EB80E479');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT FK_FFD09443EB80E479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE bl_translation_type DROP CONSTRAINT fk_ffd09443eb80e479');
		$this->addSql('ALTER TABLE bl_translation_type ADD CONSTRAINT fk_ffd09443eb80e479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (bl_communication_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5abf8512177');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5abeb80e479');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5abfe9b05dc');
		$this->addSql('ALTER TABLE bl_call DROP CONSTRAINT fk_a9fae5ab45de1a76');
		$this->addSql('ALTER TABLE bl_call ALTER bl_contact_id DROP NOT NULL');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abf8512177 FOREIGN KEY (bl_service_type_id) REFERENCES bl_service_type (bl_service_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abeb80e479 FOREIGN KEY (bl_communication_type_id) REFERENCES bl_communication_type (bl_communication_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5abfe9b05dc FOREIGN KEY (bl_source_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
		$this->addSql('ALTER TABLE bl_call ADD CONSTRAINT fk_a9fae5ab45de1a76 FOREIGN KEY (bl_target_language_id) REFERENCES bl_language (bl_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}
}
