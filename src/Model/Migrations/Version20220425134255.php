<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220425134255 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE public.customer_invoice_item
            ADD COLUMN original_item_id bigint');

		$this->addSql('ALTER TABLE public.tm_savings_item
            ADD COLUMN old_quantity numeric ( 19, 3 ),
            ADD COLUMN old_fixed_rate numeric ( 19, 5 ),
            ADD COLUMN old_percentage_rate numeric ( 19, 5 )');

		$this->addSql('ALTER TABLE public.tm_savings
            ADD COLUMN old_base_rate numeric ( 19, 5 )');

		$this->addSql('ALTER TABLE public.customer_invoice
            ADD COLUMN original_invoice_id bigint,
            ADD COLUMN credit_note_number character varying ( 255 ),
            ADD COLUMN reason_for_correction_id bigint,
            ADD COLUMN credit_note_date date,
            ADD COLUMN credit_note_number_modified boolean,
            ADD COLUMN invoice_recipient_id bigint,
            ADD COLUMN original_tasks_value numeric ( 19, 2 )');

		$this->addSql('ALTER TABLE public.task_charge
            ADD COLUMN old_rate numeric ( 19, 5 ),
            ADD COLUMN old_quantity numeric ( 19, 3 ),
            ADD COLUMN old_rate_origin character varying ( 255 )');

		$this->addSql('ALTER TABLE public.task_cat_charge
            ADD COLUMN old_rate_origin character varying ( 255 )');

		$this->addSql('ALTER TABLE public.customer
            ADD COLUMN credit_note_numbering_schema_id bigint,
            ADD COLUMN credit_note_template_id bigint,
            ADD COLUMN client_portal_interface_locale character varying ( 255 )');
	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
		$this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('ALTER TABLE public.customer_invoice_item
            DROP COLUMN original_item_id');

		$this->addSql('ALTER TABLE public.tm_savings_item
            DROP COLUMN old_quantity,
            DROP COLUMN old_fixed_rate,
            DROP COLUMN old_percentage_rate');

		$this->addSql('ALTER TABLE public.tm_savings
            DROP COLUMN old_base_rate');

		$this->addSql('ALTER TABLE public.customer_invoice
            DROP COLUMN original_invoice_id,
            DROP COLUMN credit_note_number,
            DROP COLUMN reason_for_correction_id,
            DROP COLUMN credit_note_date,
            DROP COLUMN credit_note_number_modified,
            DROP COLUMN invoice_recipient_id,
            DROP COLUMN original_tasks_value');

		$this->addSql('ALTER TABLE public.task_charge
            DROP COLUMN old_rate,
            DROP COLUMN old_quantity,
            DROP COLUMN old_rate_origin');

		$this->addSql('ALTER TABLE public.task_cat_charge
            DROP COLUMN old_rate_origin');

		$this->addSql('ALTER TABLE public.customer
            DROP COLUMN credit_note_numbering_schema_id,
            DROP COLUMN credit_note_template_id,
            DROP COLUMN client_portal_interface_locale');
	}
}
