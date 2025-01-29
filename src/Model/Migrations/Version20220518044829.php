<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220518044829 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE xtm_metrics (id UUID NOT NULL, analytics_project_id BIGINT NOT NULL, external_id INT NOT NULL, target_language_code VARCHAR(50) NOT NULL, ice_match_characters INT NOT NULL, ice_match_segments INT NOT NULL, ice_match_words INT NOT NULL, ice_match_whitespaces INT NOT NULL, low_fuzzy_match_characters INT NOT NULL, low_fuzzy_match_segments INT NOT NULL, low_fuzzy_match_words INT NOT NULL, low_fuzzy_match_whitespaces INT NOT NULL, medium_fuzzy_match_characters INT NOT NULL, medium_fuzzy_match_segments INT NOT NULL, medium_fuzzy_match_words INT NOT NULL, medium_fuzzy_match_whitespaces INT NOT NULL, high_fuzzy_match_characters INT NOT NULL, high_fuzzy_match_segments INT NOT NULL, high_fuzzy_match_words INT NOT NULL, high_fuzzy_match_whitespaces INT NOT NULL, repeats_characters INT NOT NULL, repeats_segments INT NOT NULL, repeats_words INT NOT NULL, repeats_whitespaces INT NOT NULL, leveraged_characters INT NOT NULL, leveraged_segments INT NOT NULL, leveraged_words INT NOT NULL, leveraged_whitespaces INT NOT NULL, low_fuzzy_repeats_characters INT NOT NULL, low_fuzzy_repeats_segments INT NOT NULL, low_fuzzy_repeats_words INT NOT NULL, low_fuzzy_repeats_whitespaces INT NOT NULL, medium_fuzzy_repeats_characters INT NOT NULL, medium_fuzzy_repeats_segments INT NOT NULL, medium_fuzzy_repeats_words INT NOT NULL, medium_fuzzy_repeats_whitespaces INT NOT NULL, high_fuzzy_repeats_characters INT NOT NULL, high_fuzzy_repeats_segments INT NOT NULL, high_fuzzy_repeats_words INT NOT NULL, high_fuzzy_repeats_whitespaces INT NOT NULL, non_translatable_characters INT NOT NULL, non_translatable_segments INT NOT NULL, non_translatable_words INT NOT NULL, non_translatable_whitespaces INT NOT NULL, total_characters INT NOT NULL, total_segments INT NOT NULL, total_words INT NOT NULL, total_whitespaces INT NOT NULL, machine_translation_characters INT NOT NULL, machine_translation_segments INT NOT NULL, machine_translation_words INT NOT NULL, machine_translation_whitespaces INT NOT NULL, no_match_characters INT NOT NULL, no_match_segments INT NOT NULL, no_match_words INT NOT NULL, no_match_whitespaces INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_4E08D5B86D9546F ON xtm_metrics (analytics_project_id)');
		$this->addSql('ALTER TABLE xtm_metrics ADD CONSTRAINT FK_4E08D5B86D9546F FOREIGN KEY (analytics_project_id) REFERENCES analytics_project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE xtm_metrics');
	}
}
