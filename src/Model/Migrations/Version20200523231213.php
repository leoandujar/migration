<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Model\Entity\Customer;
use App\Model\Entity\CPSetting;
use Doctrine\DBAL\Schema\Schema;
use App\Model\Entity\CPSettingProject;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

final class Version20200523231213 extends AbstractMigration
{
	private $em;

	public function __construct(Connection $connection, LoggerInterface $logger)
	{
		parent::__construct($connection, $logger);
	}

	public function up(Schema $schema): void
	{
		$customers = $this->getCustomerList();
		$em        = $this->em;

		foreach ($customers as $customerId) {
			$customerObj    = $em->getRepository(Customer::class)->find($customerId);
			$settingProject = new CPSettingProject();
			$settingProject
				->setWorkingFilesAsRefFiles(true)
				->setUpdateWorkingFiles(true)
				->setConfirmationSendByDefault(false)
				->setDownloadConfirmation(true)
				->setDeadlineOptions([])
				->setQuickEstimate(false)
				->setCustomFields(json_decode($this->getCustomFields(), true));
			$em->persist($settingProject);
			$setting = new CPSetting();
			$setting
				->setCustomer($customerObj)
				->setProjectSettings($settingProject);
			$em->persist($setting);
			
		}
    $em->flush();

	}

	public function down(Schema $schema): void
	{
		// this down() migration is auto-generated, please modify it to your needs
	}

	private function getCustomerList(): array
	{
		$qb  = $this->connection->createQueryBuilder();
		$res = $qb
			->select('cust.customer_id')
			->from('customer', 'cust')
			->leftJoin('cust', 'cp_setting', 'sett', 'sett.customer_id = cust.customer_id')
			->where($qb->expr()->isNull('sett.customer_id'))
			->execute();

		$ids = [];
		for ($row = $res->fetch(); $row; $row = $res->fetch()) {
			$ids[] = $row['customer_id'];
		}

		return $ids;
	}

	private function getCustomFields(): string
	{
		return '[
    {
      "type": "CHECKBOX",
      "name": "RUSH",
      "key": "rush",
      "value": false
    },
    {
      "type": "TEXT",
      "name": "Cost Center - Project ",
      "key": "cost_center",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "OTN Number ",
      "key": "otn_number",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "Billing NUID",
      "key": "nuid",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "Billing Contact ",
      "key": "billing_contact",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "PR- AccStatus",
      "key": "pr_acc_status",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "Purpose",
      "key": "purpose",
      "value": ""
    },
    {
      "type": "SELECTION",
      "name": "Domain",
      "key": "domain",
      "value": ""
    },
    {
      "type": "SELECTION",
      "name": "Genre",
      "key": "genre",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "Function",
      "key": "function",
      "value": ""
    },
    {
      "type": "TEXT",
      "name": "Audience",
      "key": "audience",
      "value": ""
    },
    {
      "type": "SELECTION",
      "name": "Template",
      "key": "template",
      "value": null
    }
   ]';
	}
}
