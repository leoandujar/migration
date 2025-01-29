<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201212002617 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('ALTER TABLE hs_contact_person ALTER first_url TYPE TEXT');
		$this->addSql('ALTER TABLE hs_contact_person ALTER first_url DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_contact_person ALTER last_name DROP NOT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ALTER first_name DROP NOT NULL');
		$this->addSql('ALTER TABLE hs_deal ALTER estimated_rfp_amount TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE hs_deal ALTER estimated_rfp_amount DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email DROP publish_date');
		$this->addSql('ALTER TABLE hs_marketing_email ADD publish_date BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_marketing_email DROP updated_date');
		$this->addSql('ALTER TABLE hs_marketing_email ADD updated_date BIGINT DEFAULT NULL');
		$this->addSql('ALTER TABLE hs_marketing_email DROP created_date');
		$this->addSql('ALTER TABLE hs_marketing_email ADD created_date BIGINT DEFAULT NULL');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE hs_contact_person ALTER first_url TYPE VARCHAR(255)');
		$this->addSql('ALTER TABLE hs_contact_person ALTER first_url DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_contact_person ALTER last_name SET NOT NULL');
		$this->addSql('ALTER TABLE hs_contact_person ALTER first_name SET NOT NULL');
		$this->addSql('ALTER TABLE hs_deal ALTER estimated_rfp_amount TYPE NUMERIC(16, 2)');
		$this->addSql('ALTER TABLE hs_deal ALTER estimated_rfp_amount DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER publish_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER publish_date DROP DEFAULT');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER updated_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER updated_date DROP DEFAULT');
		$this->addSql('CREATE UNIQUE INDEX uniq_13e7151462b6a46e ON xtrf_user (hs_owner_id)');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER created_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
		$this->addSql('ALTER TABLE hs_marketing_email ALTER created_date DROP DEFAULT');
		$this->addSql('CREATE UNIQUE INDEX uniq_13e7151462b6a46e ON xtrf_user (hs_owner_id)');
	}
}
