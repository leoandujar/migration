<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221021225249 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER INDEX idx_651985fbff562e61 RENAME TO IDX_8C79057AFF562E61');
		$this->addSql('ALTER INDEX idx_651985fbfd4f9daa RENAME TO IDX_8C79057AFD4F9DAA');
		$this->addSql('ALTER INDEX idx_122c91b5727aca70 RENAME TO IDX_DD0C52FF727ACA70');
		$this->addSql('ALTER INDEX idx_122c91b5ff562e61 RENAME TO IDX_DD0C52FFFF562E61');
		$this->addSql('ALTER INDEX idx_603eb0d1cae716d RENAME TO IDX_5265F0B1CAE716D');
		$this->addSql('ALTER INDEX idx_603eb0dff562e61 RENAME TO IDX_5265F0BFF562E61');
		$this->addSql('ALTER INDEX idx_603eb0dbf396750 RENAME TO IDX_5265F0BBF396750');
		$this->addSql('ALTER INDEX idx_7bebb85a81c06096 RENAME TO IDX_928B38DB81C06096');
		$this->addSql('ALTER INDEX idx_7bebb85a1cae716d RENAME TO IDX_928B38DB1CAE716D');
		$this->addSql('ALTER INDEX idx_47cc8c9277153098 RENAME TO IDX_E4BDAD3177153098');
		$this->addSql('ALTER INDEX idx_47cc8c925e237e06 RENAME TO IDX_E4BDAD315E237E06');
		$this->addSql('ALTER INDEX idx_47cc8c92466f2ffc RENAME TO IDX_E4BDAD31466F2FFC');
		$this->addSql('ALTER INDEX uniq_47cc8c9277153098466f2ffc RENAME TO UNIQ_E4BDAD3177153098466F2FFC');
		$this->addSql('ALTER INDEX idx_e04992aa9d32f035 RENAME TO IDX_FFE3B2739D32F035');
		$this->addSql('ALTER INDEX idx_e04992aad60322ac RENAME TO IDX_FFE3B273D60322AC');
		$this->addSql('ALTER INDEX idx_e04992aabf7692a3 RENAME TO IDX_FFE3B273BF7692A3');
		$this->addSql('ALTER INDEX idx_e04992aa9ffdf951 RENAME TO IDX_FFE3B2739FFDF951');
		$this->addSql('ALTER INDEX idx_e04992aa2d0e18d3 RENAME TO IDX_FFE3B2732D0E18D3');
		$this->addSql('ALTER INDEX idx_c118dcc0e5562a2a RENAME TO IDX_B63136E1E5562A2A');
		$this->addSql('ALTER INDEX idx_c118dcc08bf1fb99 RENAME TO IDX_B63136E18BF1FB99');
		$this->addSql('ALTER INDEX idx_db1f62a05da0fb8 RENAME TO IDX_807634D95DA0FB8');
		$this->addSql('ALTER INDEX idx_db1f62a0bef83e0a RENAME TO IDX_807634D9BEF83E0A');
		$this->addSql('ALTER INDEX uniq_db1f62a05da0fb8bef83e0a RENAME TO UNIQ_807634D95DA0FB8BEF83E0A');
		$this->addSql('ALTER INDEX idx_57698a6a77153098 RENAME TO IDX_A1B87B9F77153098');
		$this->addSql('ALTER INDEX idx_57698a6a5e237e06 RENAME TO IDX_A1B87B9F5E237E06');
		$this->addSql('ALTER INDEX idx_57698a6a466f2ffc RENAME TO IDX_A1B87B9F466F2FFC');
		$this->addSql('ALTER INDEX uniq_57698a6a77153098466f2ffc RENAME TO UNIQ_A1B87B9F77153098466F2FFC');
		$this->addSql('ALTER INDEX idx_61134782bf7692a3 RENAME TO IDX_7B4227BCBF7692A3');
		$this->addSql('ALTER INDEX idx_611347827b00651c RENAME TO IDX_7B4227BC7B00651C');
		$this->addSql('ALTER INDEX idx_61134782b23db7b8 RENAME TO IDX_7B4227BCB23DB7B8');
		$this->addSql('ALTER INDEX uniq_61134782e7927c74 RENAME TO UNIQ_7B4227BCE7927C74');
		$this->addSql('ALTER INDEX uniq_61134782f85e0677 RENAME TO UNIQ_7B4227BCF85E0677');
		$this->addSql('ALTER INDEX uniq_8fde25802c7c2cba RENAME TO UNIQ_93A9D00E2C7C2CBA');
		$this->addSql('ALTER INDEX idx_85115c10166d1f9c RENAME TO IDX_BF1C29E7166D1F9C');
		$this->addSql('ALTER INDEX idx_85115c108db60186 RENAME TO IDX_BF1C29E78DB60186');
		$this->addSql('ALTER INDEX idx_85115c10ce6064c2 RENAME TO IDX_BF1C29E7CE6064C2');
		$this->addSql('ALTER INDEX idx_85115c1082f1baf4 RENAME TO IDX_BF1C29E782F1BAF4');
		$this->addSql('ALTER INDEX idx_85115c1081c06096 RENAME TO IDX_BF1C29E781C06096');
		$this->addSql('ALTER INDEX idx_a4b70cf66d9546f RENAME TO IDX_2F339D3F6D9546F');
		$this->addSql('ALTER INDEX idx_a4b70cf6ce6064c2 RENAME TO IDX_2F339D3FCE6064C2');
		$this->addSql('ALTER INDEX idx_3832de406d9546f RENAME TO IDX_C5AB9FF46D9546F');
		$this->addSql('ALTER INDEX idx_3832de408eb20258 RENAME TO IDX_C5AB9FF48EB20258');
		$this->addSql('ALTER INDEX idx_7ff33cd4727aca70 RENAME TO IDX_EACCB1D7727ACA70');
		$this->addSql('ALTER INDEX idx_38be669e727aca70 RENAME TO IDX_B33AF757727ACA70');
		$this->addSql('ALTER INDEX idx_38be669ebb8682fa RENAME TO IDX_B33AF757BB8682FA');
		$this->addSql('ALTER TABLE av_report_chart DROP CONSTRAINT IF EXISTS FK_A4DE6C67FFF2BAD2');
		$this->addSql('ALTER TABLE av_report_chart ADD CONSTRAINT FK_A4DE6C67FFF2BAD2 FOREIGN KEY (report_type) REFERENCES av_report_type (av_report_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER INDEX idx_bf1c29e7ce6064c2 RENAME TO idx_85115c10ce6064c2');
		$this->addSql('ALTER INDEX idx_bf1c29e782f1baf4 RENAME TO idx_85115c1082f1baf4');
		$this->addSql('ALTER INDEX idx_bf1c29e781c06096 RENAME TO idx_85115c1081c06096');
		$this->addSql('ALTER INDEX idx_bf1c29e7166d1f9c RENAME TO idx_85115c10166d1f9c');
		$this->addSql('ALTER INDEX idx_bf1c29e78db60186 RENAME TO idx_85115c108db60186');
		$this->addSql('ALTER INDEX idx_2f339d3f6d9546f RENAME TO idx_a4b70cf66d9546f');
		$this->addSql('ALTER INDEX idx_2f339d3fce6064c2 RENAME TO idx_a4b70cf6ce6064c2');
		$this->addSql('ALTER INDEX uniq_7b4227bce7927c74 RENAME TO uniq_61134782e7927c74');
		$this->addSql('ALTER INDEX idx_7b4227bcb23db7b8 RENAME TO idx_61134782b23db7b8');
		$this->addSql('ALTER INDEX idx_7b4227bcbf7692a3 RENAME TO idx_61134782bf7692a3');
		$this->addSql('ALTER INDEX idx_7b4227bc7b00651c RENAME TO idx_611347827b00651c');
		$this->addSql('ALTER INDEX uniq_7b4227bcf85e0677 RENAME TO uniq_61134782f85e0677');
		$this->addSql('ALTER INDEX idx_b63136e18bf1fb99 RENAME TO idx_c118dcc08bf1fb99');
		$this->addSql('ALTER INDEX idx_b63136e1e5562a2a RENAME TO idx_c118dcc0e5562a2a');
		$this->addSql('ALTER INDEX uniq_e4bdad3177153098466f2ffc RENAME TO uniq_47cc8c9277153098466f2ffc');
		$this->addSql('ALTER INDEX idx_e4bdad31466f2ffc RENAME TO idx_47cc8c92466f2ffc');
		$this->addSql('ALTER INDEX idx_e4bdad3177153098 RENAME TO idx_47cc8c9277153098');
		$this->addSql('ALTER INDEX idx_e4bdad315e237e06 RENAME TO idx_47cc8c925e237e06');
		$this->addSql('ALTER INDEX idx_8c79057afd4f9daa RENAME TO idx_651985fbfd4f9daa');
		$this->addSql('ALTER INDEX idx_8c79057aff562e61 RENAME TO idx_651985fbff562e61');
		$this->addSql('ALTER INDEX idx_807634d9bef83e0a RENAME TO idx_db1f62a0bef83e0a');
		$this->addSql('ALTER INDEX uniq_807634d95da0fb8bef83e0a RENAME TO uniq_db1f62a05da0fb8bef83e0a');
		$this->addSql('ALTER INDEX idx_807634d95da0fb8 RENAME TO idx_db1f62a05da0fb8');
		$this->addSql('ALTER INDEX idx_5265f0b1cae716d RENAME TO idx_603eb0d1cae716d');
		$this->addSql('ALTER INDEX idx_5265f0bff562e61 RENAME TO idx_603eb0dff562e61');
		$this->addSql('ALTER INDEX idx_5265f0bbf396750 RENAME TO idx_603eb0dbf396750');
		$this->addSql('ALTER INDEX idx_eaccb1d7727aca70 RENAME TO idx_7ff33cd4727aca70');
		$this->addSql('ALTER INDEX idx_c5ab9ff46d9546f RENAME TO idx_3832de406d9546f');
		$this->addSql('ALTER INDEX idx_c5ab9ff48eb20258 RENAME TO idx_3832de408eb20258');
		$this->addSql('ALTER INDEX idx_b33af757727aca70 RENAME TO idx_38be669e727aca70');
		$this->addSql('ALTER INDEX idx_b33af757bb8682fa RENAME TO idx_38be669ebb8682fa');
		$this->addSql('ALTER INDEX idx_ffe3b2732d0e18d3 RENAME TO idx_e04992aa2d0e18d3');
		$this->addSql('ALTER INDEX idx_ffe3b2739d32f035 RENAME TO idx_e04992aa9d32f035');
		$this->addSql('ALTER INDEX idx_ffe3b2739ffdf951 RENAME TO idx_e04992aa9ffdf951');
		$this->addSql('ALTER INDEX idx_ffe3b273bf7692a3 RENAME TO idx_e04992aabf7692a3');
		$this->addSql('ALTER INDEX idx_ffe3b273d60322ac RENAME TO idx_e04992aad60322ac');
		$this->addSql('ALTER INDEX uniq_a1b87b9f77153098466f2ffc RENAME TO uniq_57698a6a77153098466f2ffc');
		$this->addSql('ALTER INDEX idx_a1b87b9f466f2ffc RENAME TO idx_57698a6a466f2ffc');
		$this->addSql('ALTER INDEX idx_a1b87b9f77153098 RENAME TO idx_57698a6a77153098');
		$this->addSql('ALTER INDEX idx_a1b87b9f5e237e06 RENAME TO idx_57698a6a5e237e06');
		$this->addSql('ALTER INDEX uniq_93a9d00e2c7c2cba RENAME TO uniq_8fde25802c7c2cba');
		$this->addSql('ALTER INDEX idx_dd0c52ffff562e61 RENAME TO idx_122c91b5ff562e61');
		$this->addSql('ALTER INDEX idx_dd0c52ff727aca70 RENAME TO idx_122c91b5727aca70');
		$this->addSql('ALTER INDEX idx_928b38db81c06096 RENAME TO idx_7bebb85a81c06096');
		$this->addSql('ALTER INDEX idx_928b38db1cae716d RENAME TO idx_7bebb85a1cae716d');
	}
}
