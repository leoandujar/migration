<?php

namespace App\Workflow\HelperServices;

use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Connector\Xtrf\XtrfConnector;
use App\Model\Entity\WFParams;
use App\Model\Entity\WorkflowJobFile;
use App\Model\Repository\ContactPersonRepository;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use App\Service\Xtrf\XtrfProjectService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Workflow\Services\XtrfProject\Start;
use App\Command\Services\Helper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProjectWorkflowService
{
	public const DEADLINE_FORMAT_DATETIME = 1;
	public const DEADLINE_FORMAT_TIMESTAMP = 2;
	public const DEADLINE_FORMAT_STRING = 3;

	private LoggerService $loggerSrv;
	private XtrfConnector $xtrfConnector;
	private XtrfQuoteService $xtrfQuoteSrv;
	private XtrfProjectService $xtrfProjectSrv;
	private NotificationService $notificationSrv;
	private ContactPersonRepository $contactPersonRepo;
	private CustomerPortalConnector $customerPortalConnector;

	public function __construct(
		LoggerService $loggerSrv,
		XtrfConnector $xtrfConnector,
		XtrfQuoteService $xtrfQuoteSrv,
		XtrfProjectService $xtrfProjectSrv,
		NotificationService $notificationSrv,
		ContactPersonRepository $contactPersonRepo,
		CustomerPortalConnector $customerPortalConnector,
	) {
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->loggerSrv = $loggerSrv;
		$this->xtrfConnector = $xtrfConnector;
		$this->xtrfProjectSrv = $xtrfProjectSrv;
		$this->contactPersonRepo = $contactPersonRepo;
		$this->customerPortalConnector = $customerPortalConnector;
		$this->notificationSrv = $notificationSrv;
	}

	public function prepareData(WFParams $parameter, string $name, array $params = null): array
	{
		$notificationTarget = $parameter->getNotificationTarget();
		$params = $params ?? $parameter->getParams();
		$params['ready_files'] = key_exists('ready_files', $params) ? $params['ready_files'] : [];
		$name = $this->defineName($name, $params);
		switch ($params['type']) {
			case Start::TYPE_PROJECT:
				if (isset($params['template']['contact_person'])) {
					$params['contact_person'] = $params['template']['contact_person'];
					unset($params['template']['contact_person']);
				}

				if (isset($params['template']['project_manager'])) {
					$projectManager = $params['template']['project_manager'];
					unset($params['template']['project_manager']);
					$params['template']['people'] = [
						'responsiblePersons' => [
							'projectManagerId' => $projectManager,
						],
					];

					if (isset($params['template']['project_coordinator'])) {
						$projectCoordinator = $params['template']['project_coordinator'];
						unset($params['template']['project_coordinator']);
						$params['template']['people']['responsiblePersons']['projectCoordinatorId'] = $projectCoordinator;
					}
				}

				$params['template']['sourceLanguageId'] = $params['template']['source_language'] ?? [];
				$params['template']['targetLanguagesIds'] = $params['template']['target_languages'] ?? [];
				$params['template']['specializationId'] = $params['template']['specialization'] ?? [];
				$params['template']['serviceId'] = $params['template']['service'] ?? [];
				$params['template']['categoriesIds'] = $params['template']['categories'] ?? [];
				$params['template']['instructions'] = $params['template']['instructions'] ?? null;
				$params['template']['name'] = $name;
				$params['ready_files'] = key_exists('ready_files', $params) ? $params['ready_files'] : [];
				unset($params['template']['source_language']);
				unset($params['template']['target_languages']);
				unset($params['template']['specialization']);
				unset($params['template']['service']);
				unset($params['template']['categories']);
				$params['template']['dates'] = [
					'startDate' => ['time' => (new \DateTime())->getTimestamp() * 1000],
					'deadline' => ['time' => $this->getDeadline($params, ProjectWorkflowService::DEADLINE_FORMAT_TIMESTAMP)],
				];
				$params['template']['inputFiles'] = [];

				return array_merge([
					'statistics' => [
						'processedFiles' => 0,
						'totalFiles' => 0,
						'errorFiles' => 0,
					],
					'files' => [],
					'sourceDisk' => $params['sourceDisk'],
					'sourcePath' => $params['sourcePath'],
					'ocr' => $params['ocr'],
					'notification_target' => $notificationTarget,
					'custom_fields' => $params['customFields'] ?? [],
					'notification_type' => $parameter->getNotificationType(),
				], $params);
			case Start::TYPE_QUOTE:
				try {
					$params = array_merge($params, [
						'statistics' => [
							'processedFiles' => 0,
							'totalFiles' => 0,
							'errorFiles' => 0,
						],
						'request' => [
							'link' => $params['download_link_url'] ?? '',
						],
						'sourceDisk' => $params['sourceDisk'],
						'sourcePath' => $params['sourcePath'],
						'ocr' => $params['ocr'],
						'notification_target' => $notificationTarget,
						'custom_fields' => $params['custom_fields'] ?? [],
						'notification_type' => $parameter->getNotificationType(),
					]);
					$params['template']['name'] = $name;
					$params['template']['custom_fields'] = $params['custom_fields'] ?? [];
					$params['template']['deliveryDate'] = $this->getDeadline($params, ProjectWorkflowService::DEADLINE_FORMAT_STRING);
					$params['instructions'] = $params['template']['instructions'] ?? null;
					unset($params['template']['instructions']);
					$params['sessionID'] = $this->xtrfQuoteSrv->xtrfLoginWithToken($params['template']['contact_person']);

					return $params;
				} catch (\Throwable $thr) {
					$this->sendErrorNotification($params, 'Quote');
					throw $thr;
				}
		}

		return [];
	}

	private function sendErrorNotification(array $params, string $target)
	{
		$target = ucfirst($target);
		if (isset($params['notification_type']) && isset($params['notification_target'])) {
			$data = [
				'message' => "Unable to create $target",
				'status' => 'failed',
				'date' => (new \DateTime())->format('Y-m-d'),
				'link' => 'No defined',
				'title' => "$target not created",
			];
			$this->notificationSrv->addNotification($params['notification_type'], $params['notification_target'], $data, '$target not created');
		}
	}

	public function configure(array $params)
	{
		switch ($params['type']) {
			case Start::TYPE_PROJECT:
				foreach ($params['files'] as $file) {
					$fileData = [
						'name' => str_replace('/', '', $file['filename']),
						'token' => $file['token'],
					];
					if (isset($file['ocr_file'])) {
						foreach ($file['ocr_file'] as $item) {
							$params['template']['inputFiles'][] = $item;
						}
					}
					if (true === boolval($params['batch'])) {
						try {
							$params['template']['inputFiles'][] = $fileData;
							$this->processCreateProjectorQuote($params);
							$params['template']['inputFiles'] = [];
						} catch (\Throwable $thr) {
							$this->loggerSrv->addError("XTRF Project failed {$file['filename']}", $thr);
							$params['template']['inputFiles'] = [];
							continue;
						}
					} else {
						$params['template']['inputFiles'][] = $fileData;
					}
				}
				break;
			case Start::TYPE_QUOTE:
				if (!count($params['files'])) {
					$this->loggerSrv->addWarning('No files to process in Quote Workflow.');
				}
				foreach ($params['files'] as $file) {
					$params['template']['inputFiles'][] = ['id' => $file['token']];
					if (isset($file['ocr_file'])) {
						foreach ($file['ocr_file'] as $item) {
							if (isset($item['name'])) {
								unset($item['name']);
							}
							$params['template']['inputFiles'][] = $item;
						}
					}
					if (true === boolval($params['batch'])) {
						try {
							$this->processCreateProjectorQuote($params);
							$params['template']['inputFiles'] = [];
						} catch (\Throwable $thr) {
							$this->loggerSrv->addError("XTRF Quote failed {$file['filename']}", $thr);
							$params['template']['inputFiles'] = [];
							continue;
						}
					}
				}
				break;
		}
		if (isset($params['template']['inputFiles']) && count($params['template']['inputFiles'])) {
			$this->processCreateProjectorQuote($params);
		}
	}

	private function processCreateProjectorQuote(&$context)
	{
		if (is_array($context['files']) && count($context['files'])) {
			switch ($context['type']) {
				case Start::TYPE_PROJECT:
					if (empty($context['contact_person'])) {
						$msg = 'Missing contact_person param for WF Project.';
						$this->loggerSrv->addError($msg);
						throw new BadRequestHttpException($msg);
					}

					$contactPersonId = $context['contact_person'];
					$contactPerson = $this->contactPersonRepo->find($contactPersonId);
					$context['template']['customerId'] = $contactPerson?->getCustomersPerson()?->getCustomer()?->getId();

					$response = $this->xtrfConnector->createProject($context['template']);
					if (!$response?->isSuccessfull() && null !== $response->getProject()) {
						$msg = 'Unable to create Project on XTRF for Project WF.';
						$this->loggerSrv->addError($msg, [
							'raw' => print_r($response->getRaw(), true),
							'detailedMessage' => $response->getDetailedMessage(),
							'errorMessage' => $response->getErrorMessage(),
						]);
						throw new BadRequestHttpException($msg);
					}

					$responseAdditionalContact = $this->xtrfConnector->additionalContactPerson($response->getProject()->id, ['primaryId' => $contactPersonId]);
					if (!$responseAdditionalContact?->isSuccessfull()) {
						$this->loggerSrv->addWarning("Unable to link additional contact person to project {$response->getProject()->id}");
					}
					$context['request']['data'][] = [
						'name' => $response->getProject()->name,
						'info' => $response->getProject(),
					];
					$context['request']['links'][] = $context['download_link_url'].$response->getProject()->id;
					$this->xtrfConnector->updateProjectCustomFields($response->getProject()->id, $context['custom_fields']);
					if ($context['workingFilesAsRefFiles'] && !empty($context['template']['inputFiles'])) {
						foreach ($context['template']['inputFiles'] as $ref) {
							$token = is_array($ref) && isset($ref['token']) ? $ref['token'] : $ref;
							$refData[] = [
								'token' => $token,
								'category' => WorkflowJobFile::CATEGORY_REF,
							];
						}

						if (count($refData)) {
							$projectGetResponse = $this->xtrfConnector->getProject($response->getProject()->id);
							if ($projectGetResponse->isSuccessfull()) {
								$this->xtrfProjectSrv->updateTaskFiles($refData, $projectGetResponse->getProject()?->tasks);
							}
						}
					}
					$context['info'] = sprintf('New XTRF project %s', $response->getProject()->idNumber);
					$context['status'] = 'success';
					break;
				case Start::TYPE_QUOTE:
					$context['info'] = 'Unable to create new XTRF Quote';
					$context['status'] = 'failed';
					if ($context['workingFilesAsRefFiles']) {
						$context['template']['referenceFiles'] = $context['template']['inputFiles'];
					}
					$dataParams = $this->xtrfQuoteSrv->prepareCreateData($context['template']);
					$response = $this->customerPortalConnector->createQuote($dataParams, $context['sessionID']);
					if (!$response->isSuccessfull()) {
						$msg = 'Unable to create Quote on XTRF for Quote WF.';
						$this->loggerSrv->addError($msg, [
							'raw' => print_r($response->getRaw(), true),
							'detailedMessage' => $response->getDetailedMessage(),
							'errorMessage' => $response->getErrorMessage(),
						]);
						throw new BadRequestHttpException($msg);
					}
					$quote = $response->getQuoteDto();
					if (null !== $quote) {
						if ($context['instructions']) {
							$this->xtrfConnector->updateQuoteInstructions($quote->id, $context['instructions']);
						}
						$context['request']['data'][] = [
							'name' => $quote->name,
							'info' => $quote,
						];
						$context['request']['links'][] = $context['download_link_url'].$quote->id;
						$context['info'] = sprintf('New XTRF quote %s', $quote->idNumber);
						$context['status'] = 'success';
					}
					break;
			}
		}
	}

	public function defineName(string $name, array $params): string
	{
		$name = $params['name_prefix'] ?? $name;
		if (isset($params['name_option'])) {
			$name .= match ($params['name_option']) {
				'DATE' => sprintf(' - %s', (new \DateTime())->format('D M j Y')),
			};
		}

		return $name;
	}

	public function getDeadline(array $params, int $returnType = self::DEADLINE_FORMAT_DATETIME, string $format = 'Y-m-d H:i:s'): \DateTime|int|string
	{
		$deadline = $params['deadline'];
		$deadlineTime = $params['deadline_time'] ?? null;
		if (is_numeric($deadline)) {
			$deadline = sprintf('%dD', $deadline);
		}

		$deadline = Helper::deadline($deadline, $deadlineTime);

		return match ($returnType) {
			self::DEADLINE_FORMAT_TIMESTAMP => $deadline->getTimestamp() * 1000,
			self::DEADLINE_FORMAT_STRING => $deadline->format($format),
			default => $deadline,
		};
	}
}
