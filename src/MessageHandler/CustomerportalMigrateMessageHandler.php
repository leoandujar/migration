<?php

namespace App\MessageHandler;

use App\Message\CustomerportalMigrateMessage;
use App\Model\Entity\CPTemplate;
use App\Model\Entity\Customer;
use App\Model\Repository\CustomerRepository;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CustomerportalMigrateMessageHandler
{
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;

	public function __construct(EntityManagerInterface $em, LoggerService $loggerSrv)
	{
		$this->loggerSrv = $loggerSrv;
        $this->em = $em;
	}

	public function __invoke(CustomerportalMigrateMessage $message): void
	{
		$scope = $message->getScope();

		try {
			if ('customFields' === $scope) {
				$this->migrateCustomFields(message: $message);
			} elseif ('templates' === $scope) {
				$this->migrateTemplates(message: $message);
			} else {
				$this->loggerSrv->addError('Invalid scope');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in migrating.', $thr);
		}
	}

	private function migrateTemplates(CustomerportalMigrateMessage $message): void
	{
		$inputCustomer = $message->getCustomer();
		if (null !== $inputCustomer) {
			$customers =  $this->em->getRepository(Customer::class)->getCPTemplatesByCustId(['customerId' => $inputCustomer]);
		} else {
			$customers =  $this->em->getRepository(Customer::class)->getCPTemplates();
		}

		$this->loggerSrv->addInfo('Migrating templates');

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
		}

		$this->em->flush();
		$this->loggerSrv->addInfo('Templates migrated');
	}

	private function getCustomFields($customFields): array
	{
		$decodedCustomField = json_decode(json_encode($customFields));

		$migratedCusField = [];
		if (is_array($decodedCustomField)) {
			foreach ($customFields as $field) {
				$migratedCusField[isset($field['key']) ? $field['key'] : $field['name']] = $field['value'] ?? '';
			}
		} else {
			foreach ($customFields as $section => $fields) {
				foreach ($fields as $key => $field) {
					$migratedCusField[$key] = $field['value'] ?? '';
				}
			}
		}

		return $migratedCusField;
	}

	private function migrateCustomFields(CustomerportalMigrateMessage $message): void
	{
		if (null !== $message->getCustomer()) {
			$customers = $this->em->getRepository(Customer::class)->findBy(['id' => $message->getCustomer()]);
		} else {
			$customers = $this->em->getRepository(Customer::class)->findAll();
		}

		$this->loggerSrv->addInfo('Migrating custom fields');

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
		}

		$this->em->flush();

		$this->loggerSrv->addInfo('Custom fields migrated');
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
