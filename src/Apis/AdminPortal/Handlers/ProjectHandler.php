<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Command\Command\CustomerportalFilesProjectsProcessCommand;
use App\Connector\Xtrf\XtrfConnector;
use App\Linker\Services\RedisClients;
use App\Model\Entity\Customer;
use App\Model\Entity\CustomerService;
use App\Model\Entity\InternalUser;
use App\Model\Repository\ContactPersonRepository;
use App\Model\Repository\CustomerRepository;
use App\Service\Notification\NotificationService;
use App\Service\Notification\TeamNotification;
use App\Service\RegexService;
use App\Connector\Xtrf\Dto\ProjectDto;
use App\Apis\Shared\Util\UtilsService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Repository\ProjectRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Apis\Shared\Http\Response\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class ProjectHandler
{
	private UtilsService $utilsSrv;
	private EntityManagerInterface $em;
	private RedisClients $redisClients;
	private XtrfConnector $xtrfConnector;
	private CustomerRepository $customerRepo;
	private ProjectRepository $projectRepository;
	private CloudFileSystemService $fileSystemSrv;
	private ContactPersonRepository $contactPersonRepo;
	private NotificationService $notificationSrv;
	private RequestStack $requestStack;
	private SecurityHandler $securityHandler;

	public function __construct(
		UtilsService $utilsSrv,
		EntityManagerInterface $em,
		RedisClients $redisClients,
		RequestStack $requestStack,
		XtrfConnector $xtrfConnector,
		CustomerRepository $customerRepo,
		SecurityHandler $securityHandler,
		ProjectRepository $projectRepository,
		NotificationService $notificationSrv,
		ContactPersonRepository $contactPersonRepo,
		CloudFileSystemService $fileSystemSrv,
	) {
		$this->em = $em;
		$this->utilsSrv = $utilsSrv;
		$this->customerRepo = $customerRepo;
		$this->redisClients = $redisClients;
		$this->requestStack = $requestStack;
		$this->xtrfConnector = $xtrfConnector;
		$this->notificationSrv = $notificationSrv;
		$this->securityHandler = $securityHandler;
		$this->projectRepository = $projectRepository;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->contactPersonRepo = $contactPersonRepo;
	}

	public function processGetProjects(array $params): ApiResponse
	{
		$partialName = $params['name'];
		$limit = $params['limit'];
		$customer = $params['customer_id'] ?? null;
		$status = $params['status'] ?? null;
		$archived = $params['archived'] ?? false;

		$result = $this->projectRepository->getApList(strval($partialName), intval($limit), $customer, $status, boolval($archived));

		return new ApiResponse(data: $result);
	}

	public function processGetProject(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		return new ApiResponse(data: Factory::projectDtoInstance($project));
	}

	public function processCreate(array $dataRequest): ApiResponse
	{
		/** @var InternalUser $user */
		$internalUser = $this->securityHandler->getCurrentUser($this->requestStack->getCurrentRequest());

		if (!$internalUser) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (!isset($dataRequest['contact_person_id'])) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_MISSING_PARAM,
				ApiError::$descriptions[ApiError::CODE_MISSING_PARAM],
				'contact_person_id'
			);
		}

		$user = $this->contactPersonRepo->find($dataRequest['contact_person_id']);
		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		unset($dataRequest['contact_person_id']);
		$customer = $user->getCustomersPerson()?->getCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		if (!empty($dataRequest['instructions'])) {
			$dataRequest['instructions']['fromCustomer'] = strip_tags($dataRequest['instructions']['from_customer'], RegexService::$htmlTagsAllowed);
			unset($dataRequest['instructions']['from_customer']);
		}
		$dataRequest['dates'] = [
			'startDate' => ['time' => (new \DateTime('now'))->getTimestamp() * 1000],
			'deadline' => ['time' => (new \DateTime($dataRequest['deadline']))->getTimestamp() * 1000],
		];

		if (!empty($dataRequest['customer_id'])) {
			$customer = $this->customerRepo->find($dataRequest['customer_id']);
			unset($dataRequest['customer_id']);

			if (!$customer) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
			}
		}

		if (!$customer->getUseDefaultCustomerServicesWorkflows()) {
			$customerService = $this->em->getRepository(CustomerService::class)->findOneBy(['customer' => $customer->getId(), 'service' => $dataRequest['service_id']]);

			if (!$customerService) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_service');
			}

			$workflowId = $customerService->getWorkflow()?->getId();

			if ($workflowId) {
				$targetLanguages = $dataRequest['target_languages_ids'];
				unset($dataRequest['target_languages_ids']);
			}
		}

		$dataRequest['customerId'] = $customer->getId();
		$additionalContacts = $dataRequest['additional_contacts'] ?? [];
		$sendBackTo = $dataRequest['send_back_to'] ?? null;
		$dataRequest['people'] = [
			'customerContacts' => [
				'primaryId' => $user->getId(),
				'additionalIds' => $additionalContacts,
				'sendBackToId' => $sendBackTo,
			],
			'responsiblePersons' => [
				'projectManagerId' => $customer->getInHousePmResponsible()->getId(),
			],
		];
		unset($dataRequest['additional_contacts'], $dataRequest['deadline'], $dataRequest['price_profile_id'], $dataRequest['send_back_to']);
		$customFields = [];
		$customFields[] = [
			'key' => 'source',
			'value' => 'AvantPortal',
		];

		if (!empty($dataRequest['custom_fields'])) {
			$customFields = array_merge($customFields, $dataRequest['custom_fields']);
		}

		unset($dataRequest['custom_fields']); // ANOTHER ENTRY POINT
		$this->utilsSrv->arrayKeysToCamel($dataRequest);
		$filesToUpload = [
			'inputFiles' => [],
			'referenceFiles' => [],
		];
		if (!empty($dataRequest['inputFiles'])) {
			foreach ($dataRequest['inputFiles'] as $token) {
				$fileContent = $this->redisClients->redisMainDB->hmget(
					RedisClients::SESSION_KEY_AWAITING_FILES,
					$token
				);
				if (!$fileContent) {
					continue;
				}
				if (is_array($fileContent)) {
					$fileContent = array_shift($fileContent);
				}
				if (!$fileContent) {
					$value = $this->redisClients->redisMainDB->hget(
						RedisClients::SESSION_KEY_PENDING_FILES,
						$token
					);
					if ($value) {
						$fileContent = $value;
					}
				}

				if ($fileContent) {
					$filesToUpload['inputFiles'][] = unserialize($fileContent);
				}
			}
		}
		if (!empty($dataRequest['referenceFiles'])) {
			foreach ($dataRequest['referenceFiles'] as $token) {
				$fileContent = $this->redisClients->redisMainDB->hmget(
					RedisClients::SESSION_KEY_AWAITING_FILES,
					$token
				);
				if (!$fileContent) {
					continue;
				}
				if (is_array($fileContent)) {
					$fileContent = array_shift($fileContent);
				}
				if (!$fileContent) {
					$value = $this->redisClients->redisMainDB->hget(
						RedisClients::SESSION_KEY_PENDING_FILES,
						$token
					);
					if ($value) {
						$fileContent = $value;
					}
				}

				if ($fileContent) {
					$filesToUpload['referenceFiles'][] = unserialize($fileContent);
				}
			}
		}
		unset($dataRequest['inputFiles'], $dataRequest['referenceFiles']);
		$projectCreateResponse = $this->xtrfConnector->createProject($dataRequest);
		if (!$projectCreateResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$project = $projectCreateResponse->getProject();
		$taskIds = [];
		if (!empty($workflowId) && isset($targetLanguages) && $project instanceof ProjectDto) {
			foreach ($targetLanguages as $targetLanguage) {
				$dataCreate = [
					'specializationId' => $project->specializationId,
					'workflowId' => $workflowId,
					'name' => $project->name,
					'languageCombination' => [
						'sourceLanguageId' => $dataRequest['sourceLanguageId'],
						'targetLanguageId' => $targetLanguage,
					],
					'dates' => [
						'startDate' => ['time' => (new \DateTime('now'))->getTimestamp() * 1000],
					],
				];

				$createResponse = $this->xtrfConnector->createAdditionalTaskRequest($project->id, $dataCreate);

				if (!$createResponse->isSuccessfull()) {
					return new ErrorResponse(
						Response::HTTP_BAD_GATEWAY,
						ApiError::CODE_XTRF_COMMUNICATION_ERROR,
						ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
					);
				}

				if ($createResponse->getRaw()['id']){
		            $taskIds[]=$createResponse->getRaw()['id'];
	            }
			}
		}
		$this->processUpdateCustomFields($customer, $project->id, $customFields);
		$projectGetResponse = $this->xtrfConnector->getProject($project->id);
		if (!$projectGetResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}
		$additionalInternalPersons = $customer->getUsersPersonsResponsible();
		$sendCcToEmail2 = $customer->getSendCcToEmail2();
		$toInternal = [$customer->getInHousePmResponsible()->getEmail()];
		$toCustomer = [$user->getEmail(), $customer->getInHousePmResponsible()->getEmail()];

		if (!is_null($additionalInternalPersons)) {
			foreach ($additionalInternalPersons as $person) {
				array_push($toInternal, $person->getEmail());
			}
		}

		if ($additionalContacts) {
			foreach ($additionalContacts as $personId) {
				$person = $this->contactPersonRepo->find($personId);
				array_push($toCustomer, $person->getEmail());
			}
		}

		if ($sendCcToEmail2) {
			array_push($toCustomer, $customer->getAddressEmail2());
		}

		$data = [
			'subject' => "Avantpage new Project - {$project->idNumber}",
			'template' => 'project',
			'data' => [
				'type' => 'Project',
				'idNumber' => $project->idNumber,
				'id' => $project->id,
				'customer' => $customer->getFullName(),
				'name' => "{$user->getName()} {$user->getLastName()}",
				'deadline' => date('d-m-Y H:i', $project->dates['deadline']['time'] / 1000),
				'instructions' => $project->instructions['fromCustomer'],
				'forCustomer' => false,
			],
		];

		$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $toInternal, $data);
		$data['data']['forCustomer'] = true;
		$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $toCustomer, $data);

		if (!empty($filesToUpload) && (!empty($filesToUpload['referenceFiles']) || !empty($filesToUpload['inputFiles']))) {
			$dataToSave = array_merge(
				[
					'entityId' => $projectCreateResponse->getProject()->id,
					'EntityName' => CustomerportalFilesProjectsProcessCommand::TYPE_CP_PROJECT_EXTRA_FILES,
					'taskIds' => $taskIds,
					'owner' => $internalUser->getId(),
				],
				$filesToUpload
			);
			$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($dataToSave));

			return new ApiResponse(data: ['project' => $project], code: Response::HTTP_PARTIAL_CONTENT);
		}

		return new ApiResponse(data: ['project' => $project]);
	}

	public function processGetArchive(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);

		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		if (null === $project->getArchivedAt() || empty($project->getArchivedProjectFile())) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'data');
		}

		$textToRemove1 = '/home/jboss/xtrf/xtrf_archive/';
		$textToRemove2 = '/home/jboss/xtrf/xtrf-archive/';
		$projectFileFullPath = $project->getArchivedProjectFile();
		$filename = basename($projectFileFullPath);
		$projectCleanPath1 = $this->utilsSrv->removeSubstringFromStart($textToRemove1, $projectFileFullPath);
		$projectCleanPath2 = $this->utilsSrv->removeSubstringFromStart($textToRemove2, $projectCleanPath1);
		$projectCleanPath = $this->utilsSrv->removeSubstringFromEnd("/$filename", $projectCleanPath2);

		$this->fileSystemSrv->changeStorage(CloudFileSystemService::BUCKET_ARCHIVE);

		$filePubLink = $this->fileSystemSrv->getTemporaryUrl("$projectCleanPath/$filename");

		if (empty($filePubLink)) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_FILE_LINK_ERROR, ApiError::$descriptions[ApiError::CODE_FILE_LINK_ERROR]);
		}

		return new ApiResponse(data: ['public_link' => $filePubLink]);
	}

	public function processGetArchivePassword(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);

		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		if (null === $project->getArchivedAt() || empty($project->getArchivedProjectFilePassword())) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'data');
		}

		return new ApiResponse(data: [
			'password' => $project->getArchivedProjectFilePassword(),
		]);
	}

	private function processUpdateCustomFields(Customer $customer, string $projectId, array $customFields): void
	{
		$count = 2;
		$settings = $customer->getSettings();
		while ($count-- > 0) {
			$updateResponse = $this->xtrfConnector->updateProjectCustomFields($projectId, $customFields);
			if ($updateResponse->isSuccessfull()) {
				return;
			}
			if (0 === $count && null !== $settings && !empty($settings->getTeamWebhook())) {
				$this->notificationSrv->addNotification(
					NotificationService::NOTIFICATION_TYPE_TEAM,
					$settings->getTeamWebhook(),
					[
						'title' => 'Update Custom Fields failed.',
						'message' => sprintf(
							"Unable to update custom fields for project $projectId.".PHP_EOL.'%s',
							print_r($customFields, true)
						),
						'status' => TeamNotification::STATUS_FAILURE,
					]
				);
			}
		}
	}
}
