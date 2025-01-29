<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109102839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add quality evaluation tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE ap_quality_evaluation (ap_quality_evaluation_id UUID NOT NULL, evaluatee_id BIGINT NOT NULL, evaluator_id BIGINT NOT NULL, score NUMERIC(19, 6) DEFAULT NULL, status TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(10) NOT NULL, excellent BOOLEAN NOT NULL, comment TEXT DEFAULT NULL, PRIMARY KEY(ap_quality_evaluation_id))');
        $this->addSql('CREATE INDEX IDX_B3E8FD50C4292C65 ON ap_quality_evaluation (evaluatee_id)');
        $this->addSql('CREATE INDEX IDX_B3E8FD5043575BE2 ON ap_quality_evaluation (evaluator_id)');
        $this->addSql('CREATE INDEX IDX_B3E8FD5053E1E89 ON ap_quality_evaluation (ap_quality_evaluation_id)');
        $this->addSql('CREATE TABLE ap_quality_evaluation_record (ap_quality_evaluation_record_id UUID NOT NULL, ap_quality_evaluation_id UUID NOT NULL, quality_category_id BIGINT NOT NULL, value INT NOT NULL, comment TEXT DEFAULT NULL, PRIMARY KEY(ap_quality_evaluation_record_id))');
        $this->addSql('CREATE INDEX IDX_FFBDF60853E1E89 ON ap_quality_evaluation_record (ap_quality_evaluation_id)');
        $this->addSql('CREATE INDEX IDX_FFBDF608FF562E61 ON ap_quality_evaluation_record (quality_category_id)');
        $this->addSql('CREATE INDEX IDX_FFBDF6089F641D7D ON ap_quality_evaluation_record (ap_quality_evaluation_record_id)');
        $this->addSql('ALTER TABLE ap_quality_evaluation ADD CONSTRAINT FK_B3E8FD50C4292C65 FOREIGN KEY (evaluatee_id) REFERENCES av_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ap_quality_evaluation ADD CONSTRAINT FK_B3E8FD5043575BE2 FOREIGN KEY (evaluator_id) REFERENCES av_user (internal_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ap_quality_evaluation_record ADD CONSTRAINT FK_FFBDF60853E1E89 FOREIGN KEY (ap_quality_evaluation_id) REFERENCES ap_quality_evaluation (ap_quality_evaluation_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ap_quality_evaluation_record ADD CONSTRAINT FK_FFBDF608FF562E61 FOREIGN KEY (quality_category_id) REFERENCES ap_quality_category (quality_category_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE ap_quality_evaluation_record DROP CONSTRAINT FK_FFBDF60853E1E89');
        $this->addSql('DROP TABLE ap_quality_evaluation');
        $this->addSql('DROP TABLE ap_quality_evaluation_record');
    }
}
