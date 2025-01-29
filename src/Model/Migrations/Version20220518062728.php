<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220518062728 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE TABLE xtm_statistics (id UUID NOT NULL, step BIGINT NOT NULL, type SMALLINT NOT NULL, high_fuzzy_match_characters INT NOT NULL, high_fuzzy_match_segments INT NOT NULL, high_fuzzy_match_tracked_time INT NOT NULL, high_fuzzy_match_whitespaces INT NOT NULL, high_fuzzy_match_words INT NOT NULL, high_fuzzy_repeats_characters INT NOT NULL, high_fuzzy_repeats_segments INT NOT NULL, high_fuzzy_repeats_tracked_time INT NOT NULL, high_fuzzy_repeats_whitespaces INT NOT NULL, high_fuzzy_repeats_words INT NOT NULL, ice_match_characters INT NOT NULL, ice_match_segments INT NOT NULL, ice_match_tracked_time INT NOT NULL, ice_match_whitespaces INT NOT NULL, ice_match_words INT NOT NULL, leveraged_characters INT NOT NULL, leveraged_segments INT NOT NULL, leveraged_tracked_time INT NOT NULL, leveraged_whitespaces INT NOT NULL, leveraged_words INT NOT NULL, low_fuzzy_match_characters INT NOT NULL, low_fuzzy_match_segments INT NOT NULL, low_fuzzy_match_tracked_time INT NOT NULL, low_fuzzy_match_whitespaces INT NOT NULL, low_fuzzy_match_words INT NOT NULL, low_fuzzy_repeats_characters INT NOT NULL, low_fuzzy_repeats_segments INT NOT NULL, low_fuzzy_repeats_tracked_time INT NOT NULL, low_fuzzy_repeats_whitespaces INT NOT NULL, low_fuzzy_repeats_words INT NOT NULL, machine_translation_characters INT NOT NULL, machine_translation_segments INT NOT NULL, machine_translation_tracked_time INT NOT NULL, machine_translation_whitespaces INT NOT NULL, machine_translation_words INT NOT NULL, medium_fuzzy_match_characters INT NOT NULL, medium_fuzzy_match_segments INT NOT NULL, medium_fuzzy_match_tracked_time INT NOT NULL, medium_fuzzy_match_whitespaces INT NOT NULL, medium_fuzzy_match_words INT NOT NULL, medium_fuzzy_repeats_characters INT NOT NULL, medium_fuzzy_repeats_segments INT NOT NULL, medium_fuzzy_repeats_tracked_time INT NOT NULL, medium_fuzzy_repeats_whitespaces INT NOT NULL, medium_fuzzy_repeats_words INT NOT NULL, no_matching_characters INT NOT NULL, no_matching_segments INT NOT NULL, no_matching_tracked_time INT NOT NULL, no_matching_whitespaces INT NOT NULL, no_matching_words INT NOT NULL, non_translatable_characters INT NOT NULL, non_translatable_segments INT NOT NULL, non_translatable_tracked_time INT NOT NULL, non_translatable_whitespaces INT NOT NULL, non_translatable_words INT NOT NULL, other_non_translatable_characters INT NOT NULL, other_non_translatable_tracked_time INT NOT NULL, other_non_translatable_whitespaces INT NOT NULL, other_non_translatable_words INT NOT NULL, repeats_characters INT NOT NULL, repeats_segments INT NOT NULL, repeats_tracked_time INT NOT NULL, repeats_whitespaces INT NOT NULL, repeats_words INT NOT NULL, total_characters INT NOT NULL, total_segments INT NOT NULL, total_time INT NOT NULL, total_whitespaces INT NOT NULL, total_words INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX IDX_2E2265F643B9FE3C ON xtm_statistics (step)');
		$this->addSql('ALTER TABLE xtm_statistics ADD CONSTRAINT FK_2E2265F643B9FE3C FOREIGN KEY (step) REFERENCES analytics_project_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP TABLE xtm_statistics');
	}
}
