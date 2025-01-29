<?php

namespace App\Command\Command;

use App\Model\Entity\CPTemplate;
use App\Model\Entity\Customer;
use App\Model\Repository\CustomerRepository;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
	name: 'customerportal:migrate',
	description: 'To migrate the structure of the json column customFields CPSettingProject to the new structure.',
)]
class CustomerportalMigrateCommand extends Command
{
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;
	private CustomerRepository $customerRepo;

	public function __construct(EntityManagerInterface $em, LoggerService $loggerSrv, CustomerRepository $customerRepo)
	{
		parent::__construct();
		$this->em = $em;
		$this->customerRepo = $customerRepo;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->addOption(
				'customer',
				'c',
				InputOption::VALUE_OPTIONAL,
				'Id of the specific customer'
			)
			->addArgument('scope', InputArgument::REQUIRED, 'scope can either be customFields or templates');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$scope = $input->getArgument('scope');

		try {
			if ('customFields' === $scope) {
				$this->migrateCustomFields($input, $io);
			} elseif ('templates' === $scope) {
				$this->migrateTemplates($input, $io);
			} else {
				$io->error('Invalid scope');
				return Command::FAILURE;
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in migrating.', $thr);
			$io->error('Error in migrating');

			return Command::FAILURE;
		}

		return Command::SUCCESS;
	}

	private function migrateTemplates(InputInterface $input, SymfonyStyle $io)
	{
		if (null !== $input->getOption('customer')) {
			$customers =  $this->customerRepo->getCPTemplatesByCustId(['customerId' => $input->getOption('customer')]);
		} else {
			$customers =  $this->customerRepo->getCPTemplates();
		}

		$io->writeln('Migrating templates');
		$io->progressStart(count($customers));

		foreach ($customers as $customer) {
			$cpTemplate = $this->em->getRepository(CPTemplate::class)->find($customer['cpTemplateId']);
			$data = $cpTemplate->getData();

			$dataNew = [];

			if ('service' === array_key_first($data)) { // Check if the data is in the new format
				$dataNew['details'] = [
					'name' => $data['details']['data']['name'],
					'referenceNumber' => $data['details']['data']['reference'],
					'specialization' => $data['details']['data']['specialization']['id'],
					'customer' => $data['details']['data']['office'] ?? null,
					'notes' => $data['details']['data']['customerInstructions'],
				];

				$dataNew['languages'] = [
					'sourceLanguage' => $data['languages']['data']['sourceLanguage']['id'],
					'targetLanguages' => array_column($data['languages']['data']['targetLanguages'], 'id'),
				];

				$dataNew['additional']['customFields'] = $this->getCustomFields($data['additional']['data']['customFields']);

				$dataNew['deadline']['deliveryDate'] = null;
				$dataNew['service']['service'] = $data['service']['data']['id'];
				$dataNew['preview']['specialization'] = [
					'label' => $data['details']['data']['specialization']['name'],
					'value' => $data['details']['data']['specialization']['id'],
				];
				$dataNew['preview']['service'] = [
					'id' => $data['service']['data']['id'],
					'name' => $data['service']['data']['name'],
					'total' => 0,
					'activityType' => isset($data['service']['data']['type']) ? $data['service']['data']['type'] : (isset($data['service']['data']['activityType']) ? $data['service']['data']['activityType'] : null),
				];
				$dataNew['preview']['sourceLanguage'] = [
					'label' => $data['languages']['data']['sourceLanguage']['name'],
					'value' => $data['languages']['data']['sourceLanguage']['id'],
					'symbol' => $data['languages']['data']['sourceLanguage']['symbol'],
				];
				$dataNew['preview']['targetLanguages'] = [];
				foreach ($data['languages']['data']['targetLanguages'] as $targetLanguage) {
					$dataNew['preview']['targetLanguages'][] = [
						'label' => $targetLanguage['name'],
						'value' => $targetLanguage['id'],
						'symbol' => $targetLanguage['symbol'],
					];
				}
				$dataNew['preview']['deadline'] = null;
			} elseif ('details' === array_key_first($data)) { // Check if the data is in the new format
				$dataNew = $data;
			} else {
				$dataNew = null;
			}

			$cpTemplate->setDataNew($dataNew);
			$this->em->persist($cpTemplate);

			$io->progressAdvance();
		}

		$this->em->flush();
		$io->progressFinish();
		$io->success('Templates migrated');
	}

	private function getCustomFields($customFields): array
	{
		$decodedCustomField = json_decode(json_encode($customFields));

		$migratedCusField = [];
		if (is_array($decodedCustomField)) {
			foreach ($customFields as $field) {
				$migratedCusField[isset($field['key']) ? $field['key'] : $field['name']] = $field['value'] ?? "";
			}
		} else {
			foreach ($customFields as $section => $fields) {
				foreach ($fields as $key => $field) {
					$migratedCusField[$key] = $field['value'] ?? "";
				}
			}
		}

		return $migratedCusField;
	}

	private function migrateCustomFields(InputInterface $input, SymfonyStyle $io)
	{
		if (null !== $input->getOption('customer')) {
			$customers = $this->em->getRepository(Customer::class)->findBy(['id' => $input->getOption('customer')]);
		} else {
			$customers = $this->em->getRepository(Customer::class)->findAll();
		}

		$io->writeln('Migrating custom fields');
		$io->progressStart(count($customers));

		/** @var Customer $customer */
		foreach ($customers as $customer) {
			$cpSetting = $customer->getSettings();
			if (!$cpSetting) {
				continue;
			}

			$projectSettings = $cpSetting->getProjectSettings();
			if (!$projectSettings) {
				continue;
			}

			$customFields = $projectSettings->getCustomFields();
			$migratedFields = $this->migrateFields($customFields);
			$projectSettings->setCustomFieldsNew($migratedFields);
			$this->em->persist($projectSettings);

			$io->progressAdvance();
		}

		$this->em->flush();

		$io->progressFinish();
		$io->success('Custom fields migrated');
	}

	private function migrateFields($customFields): array
	{
		$migratedFields = [];
		if (!empty($customFields)) {
			$type = gettype(json_decode(json_encode($customFields)));
			if ('array' === $type) {
				return $customFields;
			}

			foreach ($customFields as $customField) {
				foreach ($customField as $key => $field) {
					$migratedFieldObj = [];
					if ($field['enabled']) {
						$migratedFieldObj = [
							'name' => $key,
							'enabled' => true,
							'visible' => $field['visible'] ?? false,
							'value' => $field['value'] ?? null,
						];
					}

					if (!empty($migratedFieldObj)) {
						$migratedFields[] = $migratedFieldObj;
					}
				}
			}
		}

		return $migratedFields;
	}
}
