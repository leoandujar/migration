<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\DTO\TaskDto;
use App\Connector\Xtrf\Dto\ProjectDto as XtrfProjectDto;
use App\Model\Entity\CustomerService;
use App\Model\Entity\Feedback;
use App\Model\Entity\SystemAccount;
use App\Model\Repository\CustomerRepository;
use App\Service\RegexService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Model\Entity\Task;
use App\Model\Entity\User;
use App\Model\Entity\Project;
use App\Model\Entity\Currency;
use App\Model\Entity\Customer;
use App\Model\Entity\XtrfLanguage;
use App\Model\Entity\ContactPerson;
use App\Apis\Shared\DTO\CurrencyDto;
use App\Apis\Shared\DTO\LanguageDto;
use App\Model\Entity\CustomerPerson;
use App\Model\Entity\WorkflowJobFile;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Util\UtilsService;
use App\Connector\Xtrf\XtrfConnector;
use App\Linker\Services\RedisClients;
use App\Service\Xtrf\XtrfProjectService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Repository\TaskRepository;
use App\Apis\Shared\DTO\GenericPersonDto;
use App\Model\Repository\ProjectRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Connector\ApacheTika\TikaConnector;
use App\Service\Notification\TeamNotification;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Model\Repository\TaskReviewRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Notification\NotificationService;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use App\Connector\CustomerPortal\Dto\FeedbackDto;
use App\Command\Command\CustomerportalFilesProjectsProcessCommand;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Apis\CustomerPortal\Http\Response\Project\GetProjectResponse;
use App\Model\Repository\CustomerPriceListLanguageCombinationRepository;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Apis\Shared\Util\PostmarkService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Apis\Shared\DTO\ProjectDto;
use App\Apis\Shared\Handlers\BaseHandler;
use App\Constant\DateConstant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Apis\Shared\Handlers\UtilsHandler as BaseUtilsHandler;

class ProjectHandler extends BaseHandler
{
	private const TARGET_INPUT_FILE = 1;
	private const TARGET_REF_FILE = 2;
	private const CATEGORIES_MACRO_ID = 296;

	private UtilsService $utilsSrv;
	private SessionInterface $session;
	private XtrfConnector $xtrfConn;
	private TikaConnector $tikaConnector;
	private RedisClients $redisClients;
	private PostmarkService $postmarkSrv;
	private TaskRepository $taskRepository;
	private FileSystemService $fileSystemSrv;
	private CustomerRepository $customerRepo;
	private XtrfProjectService $xtrfProjectSrv;
	private ParameterBagInterface $parameterBag;
	private TokenStorageInterface $tokenStorage;
	private NotificationService $notificationSrv;
	private ProjectRepository $projectRepository;
	private CustomerPortalConnector $clientConnector;
	private TaskReviewRepository $taskReviewRepository;
	private CloudFileSystemService $fileBucketService;
	private ContactPersonRepository $contactPersonRepository;
	private CustomerPriceListLanguageCombinationRepository $priceListlangCombRepository;
	private XtrfQuoteService $xtrfQuoteSrv;
	private RequestStack $requestStack;
	private EntityManagerInterface $em;
	private BaseUtilsHandler $baseUtilsHandler;

	public function __construct(
		UtilsService $utilsSrv,
		RequestStack $requestStack,
		RedisClients $redisClients,
		XtrfConnector $xtrfConnector,
		TikaConnector $tikaConnector,
		PostmarkService $postmarkSrv,
		TaskRepository $taskRepository,
		XtrfQuoteService $xtrfQuoteSrv,
		FileSystemService $fileSystemSrv,
		CustomerRepository $customerRepo,
		XtrfProjectService $xtrfProjectSrv,
		TokenStorageInterface $tokenStorage,
		ParameterBagInterface $parameterBag,
		ProjectRepository $projectRepository,
		NotificationService $notificationSrv,
		CustomerPortalConnector $clientConnector,
		TaskReviewRepository $taskReviewRepository,
		CloudFileSystemService $fileBucketService,
		ContactPersonRepository $contactPersonRepository,
		CustomerPriceListLanguageCombinationRepository $priceListlangCombRepository,
		EntityManagerInterface $em,
		BaseUtilsHandler $baseUtilsHandler,
	) {
		parent::__construct($requestStack, $em);
		$this->session = $requestStack->getSession();
		$this->utilsSrv = $utilsSrv;
		$this->tokenStorage = $tokenStorage;
		$this->xtrfConn = $xtrfConnector;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->taskRepository = $taskRepository;
		$this->clientConnector = $clientConnector;
		$this->projectRepository = $projectRepository;
		$this->taskReviewRepository = $taskReviewRepository;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->priceListlangCombRepository = $priceListlangCombRepository;
		$this->tikaConnector = $tikaConnector;
		$this->fileBucketService = $fileBucketService;
		$this->parameterBag = $parameterBag;
		$this->notificationSrv = $notificationSrv;
		$this->redisClients = $redisClients;
		$this->xtrfProjectSrv = $xtrfProjectSrv;
		$this->postmarkSrv = $postmarkSrv;
		$this->customerRepo = $customerRepo;
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->requestStack = $requestStack;
		$this->baseUtilsHandler = $baseUtilsHandler;
		$this->em = $em;
	}

	public function processGetProjects(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();

		if (isset($dataRequest['status'])) {
			foreach ($dataRequest['status'] as $status) {
				if (!in_array(
					$status,
					[
						Project::STATUS_REQUESTED,
						Project::STATUS_OPEN,
						Project::STATUS_CLOSED,
						Project::STATUS_CANCELLED,
						Project::STATUS_COMPLAINT,
					]
				)) {
					return new ErrorResponse(
						Response::HTTP_BAD_REQUEST,
						ApiError::CODE_INVALID_VALUE,
						ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
						'status'
					);
				}
			}
		}

		if (!empty($dataRequest['survey_status'])) {
			switch ($dataRequest['survey_status']) {
				case Project::SURVEY_ANY:
					unset($dataRequest['survey_status']);
					break;
				case Project::SURVEY_SURVEYED:
					$dataRequest['survey_status'] = true;
					break;
				case Project::SURVEY_NOT_SURVEYED:
					$dataRequest['survey_status'] = false;
					break;
				default:
					return new ErrorResponse(
						Response::HTTP_BAD_REQUEST,
						ApiError::CODE_INVALID_VALUE,
						ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
						'survey_status'
					);
			}
		}

		$officePlace = $this->getOfficeCurrentUser();
		if (empty($officePlace)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_MANAGE_POLICY_EMPTY,
				ApiError::$descriptions[ApiError::CODE_MANAGE_POLICY_EMPTY]
			);
		}

		switch ($officePlace) {
			case SystemAccount::OFFICE_ONLY_RELATED:
				$dataRequest['contact_person_id'] = [$user->getId()];
				$dataRequest['customer_id'] = [$customer->getId()];
				break;
			case SystemAccount::OFFICE_DEPARTMENT:
				$dataRequest['customer_id'] = [$customer->getId()];
				$personDepartment = $user->getPersonDepartment();
				if (!$personDepartment) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'person_department');
				}
				$contactPersonList = $this->contactPersonRepository->getListBySystemAccountDepartment($personDepartment->getId(), $customer->getId());
				foreach ($contactPersonList as $cp) {
					$id = array_shift($cp);
					if (empty($dataRequest['requested_by']) || in_array($id, $dataRequest['requested_by'])) {
						$dataRequest['contact_person_id'][] = $id;
					}
				}
				break;
			case SystemAccount::OFFICE_OFFICE:
				$dataRequest['customer_id'] = [$customer->getId()];
				break;
			case SystemAccount::OFFICE_ALL_OFFICE:
				if (!empty($dataRequest['requested_by'])) {
					$dataRequest['contact_person_id'] = $dataRequest['requested_by'];
					unset($dataRequest['requested_by']);
				}
				$customerPerson = $user->getCustomersPerson();
				if (!$customerPerson) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
				}
				$customers = $customerPerson->getCustomers();
				foreach ($customers as $cust) {
					if (empty($dataRequest['offices']) || in_array($cust->getId(), $dataRequest['offices'])) {
						$dataRequest['customer_id'][] = $cust->getId();
					}
				}
				break;
			case SystemAccount::OFFICE_ALL_OFFICE_RELATED:
				$dataRequest['contact_person_id'] = [$user->getId()];
				$customerPerson = $user->getCustomersPerson();
				if (!$customerPerson) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
				}
				$customers = $customerPerson->getCustomers();
				foreach ($customers as $cust) {
					if (empty($dataRequest['offices']) || in_array($cust->getId(), $dataRequest['offices'])) {
						$dataRequest['customer_id'][] = $cust->getId();
					}
				}
				break;
			default:
				return new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_INVALID_VALUE,
					ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
					'manage_policy'
				);
		}
		if (empty($dataRequest['customer_id'])) {
			return new ErrorResponse(
				Response::HTTP_NOT_FOUND,
				ApiError::CODE_MISSING_PARAM,
				ApiError::$descriptions[ApiError::CODE_MISSING_PARAM],
				'customer_id'
			);
		}

		$totalRows = $this->projectRepository->getCountSearchProject($dataRequest);
		$paginationDto = new PaginationDto($dataRequest['page'], $dataRequest['per_page'], $totalRows, $dataRequest['sort_order'], $dataRequest['sort_by']);
		$dataRequest['start'] = $paginationDto->from;
		unset($dataRequest['requested_by']);
		$sqlResponse = $this->projectRepository->getSearchProject($dataRequest);

		$response = new DefaultPaginationResponse(
			[
				'entities' => $this->prepareProjectSearchResponse($sqlResponse),
			]
		);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processExportProjects(array $dataRequest): BinaryFileResponse|ApiResponse
	{
		$projectsResponse = $this->processGetProjects($dataRequest);
		if ($projectsResponse instanceof ErrorResponse) {
			return $projectsResponse;
		}

		$arrayData = json_decode($projectsResponse->getContent(), true);
		$projects = $arrayData['data'];
		if (empty($projects)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$flattenedData = array_map(function ($item) {
			return $this->flatten($item);
		}, $arrayData['data']);

		$serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
		$csvData = $serializer->encode($flattenedData, 'csv');

		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'project_files');
		$filePath = $this->fileSystemSrv->filesPath.'/project_files/project_info_'.uniqid().'.csv';
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $csvData)) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filePath).'.csv');
			$response->deleteFileAfterSend();

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function flatten($array, $prefix = '')
	{
		$result = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = $result + $this->flatten($value, $prefix.$key.'.');
			} else {
				$result[$prefix.$key] = $value;
			}
		}

		return $result;
	}

	public function processGetProject(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$officeCheckerResponse = $this->checkOfficePermission($project);
		if ($officeCheckerResponse instanceof ErrorResponse) {
			return $officeCheckerResponse;
		}

		$accountManagerPic = $project->getAccountManager()?->getEntityImage();
		$projectManagerPic = $project?->getProjectManager()?->getEntityImage();
		$accountManagerPicData = null;
		$projectManagerPicData = null;
		if (null !== $accountManagerPic) {
			if (!empty($accountManagerPic->getImageData())) {
				$accountManagerPicData = $this->fileSystemSrv->getBase64ImagePngFromResource(
					$accountManagerPic->getImageData()
				);
			}
		}
		if (null !== $projectManagerPic) {
			if (!empty($projectManagerPic->getImageData())) {
				$projectManagerPicData = $this->fileSystemSrv->getBase64ImagePngFromResource(
					$projectManagerPic->getImageData()
				);
			}
		}

		$inputFiles = [];
		/** @var Task $firstTask */
		$firstTask = $project->getTasks()->first();
		if (null !== $firstTask && $firstTask instanceof Task) {
			foreach ($firstTask->getWorkflowJobFiles() as $file) {
				if (WorkflowJobFile::CATEGORY_WORKFILE === $file->getCategory()) {
					$inputFiles[] = [
						'id' => $file->getId(),
						'name' => $file->getName(),
					];
				}
			}
		}

		return new GetProjectResponse(
			[
				'project' => $project,
				'inputFiles' => $inputFiles,
				'tasks' => $project->getTasks(),
				'accountManagerPicData' => $accountManagerPicData,
				'projectManagerPicData' => $projectManagerPicData,
			]
		);
	}

	public function processGetProjectsReview(array $dataRequest): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		if (!$customer->getLinkedProviderId()) {
			return new ApiResponse(data: []);
		}

		$conResponse = $this->clientConnector->getProjectsReview();

		if (!$conResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$result = [];
		$totalRows = 0;
		$reviewProjects = $conResponse->getRaw();
		if (count($reviewProjects)) {
			$totalRows = count($reviewProjects);
			foreach ($reviewProjects as $project) {
				$projectEntity = $this->projectRepository->find($project['id']);
				if (!$projectEntity) {
					continue;
				}
				$reviewTasks = $project['tasksForReview'];
				$tasks = [];
				foreach ($reviewTasks as $task) {
					$taskEntity = $this->taskRepository->find($task['id']);
					if (!$taskEntity) {
						continue;
					}
					$taskEntity->forReview = [
						'deadline' => \DateTime::createFromFormat('U.u', $task['dueDate']['millisGMT']),
						'contactPerson' => $task['contactPerson'],
						'filesForReview' => $task['filesForReview'],
					];
					$tasks[] = $taskEntity;
				}
				$projectEntity->tasksForReview = $tasks;
				$result[] = $projectEntity;
			}
		}
		$paginationDto = new PaginationDto($dataRequest['page'], $dataRequest['per_page'], $totalRows, $dataRequest['sort_order'], $dataRequest['sort_by']);
		$response = new DefaultPaginationResponse(
			[
				'entities' => $this->prepareProjectSearchResponse($result),
			]
		);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processCreate(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();

		if (!empty($dataRequest['instructions'])) {
			$dataRequest['instructions']['fromCustomer'] = strip_tags($dataRequest['instructions']['from_customer'], RegexService::$htmlTagsAllowed);
			unset($dataRequest['instructions']['from_customer']);
		}
		$dataRequest['dates'] = [
			'startDate' => ['time' => (new \DateTime('now'))->getTimestamp() * 1000],
			'deadline' => ['time' => (new \DateTime($dataRequest['deadline']))->getTimestamp() * 1000],
		];

		if (!empty($dataRequest['customer_id'])) {
			$officePlace = $this->getOfficeCurrentUser();
			if (empty($officePlace)) {
				return new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_MANAGE_POLICY_EMPTY,
					ApiError::$descriptions[ApiError::CODE_MANAGE_POLICY_EMPTY]
				);
			}

			switch ($officePlace) {
				case SystemAccount::OFFICE_ONLY_RELATED:
				case SystemAccount::OFFICE_DEPARTMENT:
				case SystemAccount::OFFICE_OFFICE:
					if (strval($dataRequest['customer_id']) !== $customer->getId()) {
						return new ErrorResponse(
							Response::HTTP_FORBIDDEN,
							ApiError::CODE_NOT_ENOUGH_PERMISSIONS,
							ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]
						);
					}
					break;
				case SystemAccount::OFFICE_ALL_OFFICE:
					$customerIds = [];
					$customerPerson = $user->getCustomersPerson();
					if (!$customerPerson) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
					}
					$customers = $customerPerson->getCustomers();
					foreach ($customers as $cust) {
						$customerIds[] = $cust->getId();
					}
					if (!in_array(strval($dataRequest['customer_id']), $customerIds)) {
						return new ErrorResponse(
							Response::HTTP_FORBIDDEN,
							ApiError::CODE_NOT_ENOUGH_PERMISSIONS,
							ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]
						);
					}
					break;
				case SystemAccount::OFFICE_ALL_OFFICE_RELATED:
					if (strval($dataRequest['customer_id']) !== $customer->getId()) {
						return new ErrorResponse(
							Response::HTTP_FORBIDDEN,
							ApiError::CODE_NOT_ENOUGH_PERMISSIONS,
							ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]
						);
					}

					$customerIds = [];
					$customerPerson = $user->getCustomersPerson();
					if (!$customerPerson) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
					}
					$customers = $customerPerson->getCustomers();
					foreach ($customers as $cust) {
						$customerIds[] = $cust->getId();
					}
					if (!in_array(strval($dataRequest['customer_id']), $customerIds)) {
						return new ErrorResponse(
							Response::HTTP_FORBIDDEN,
							ApiError::CODE_NOT_ENOUGH_PERMISSIONS,
							ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]
						);
					}
					break;
				default:
					return new ErrorResponse(
						Response::HTTP_BAD_REQUEST,
						ApiError::CODE_INVALID_VALUE,
						ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
						'manage_policy'
					);
			}
			$customer = $this->customerRepo->find($dataRequest['customer_id']);
			unset($dataRequest['customer_id']);
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
			'name' => 'Creation source',
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
		$projectCreateResponse = $this->xtrfConn->createProject($dataRequest);
		if (!$projectCreateResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$project = $projectCreateResponse->getProject();
		$taskIds = [];
		if (!empty($workflowId) && isset($targetLanguages) && $project instanceof XtrfProjectDto) {
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

				$createResponse = $this->xtrfConn->createAdditionalTaskRequest($project->id, $dataCreate);

				if (!$createResponse->isSuccessfull()) {
					return new ErrorResponse(
						Response::HTTP_BAD_GATEWAY,
						ApiError::CODE_XTRF_COMMUNICATION_ERROR,
						ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
					);
				}
				if ($createResponse->getRaw()['id']) {
					$taskIds[] = $createResponse->getRaw()['id'];
				}
			}
		}
		$this->processUpdateCustomFields($project->id, $customFields);
		$projectGetResponse = $this->xtrfConn->getProject($project->id);
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

		foreach ($additionalInternalPersons as $person) {
			$toInternal[] = $person->getEmail();
		}

		if ($additionalContacts) {
			foreach ($additionalContacts as $personId) {
				$person = $this->contactPersonRepository->find($personId);
				$toCustomer[] = $person->getEmail();
			}
		}

		if ($sendCcToEmail2) {
			$toCustomer[] = $customer->getAddressEmail2();
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
					'owner' => $user->getId(),
				],
				$filesToUpload
			);
			$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($dataToSave));

			return new ApiResponse(code: Response::HTTP_PARTIAL_CONTENT, data: ['project' => $project]);
		}

		return new ApiResponse(data: ['project' => $project]);
	}

	public function processCreateV2(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();
		$filesToUpload = [
			'inputFiles' => [],
			'referenceFiles' => [],
		];
		$this->utilsSrv->arrayKeysToCamel($dataRequest);
		$settings = $customer->getSettings()?->getProjectSettings();
		$filesTaskMapping = [];
		if (!empty($dataRequest['inputFiles'])) {
			foreach ($dataRequest['inputFiles'] as $fileData) {
				$token = $fileData['id'] ?? null;
				$tasks = $fileData['languages_ids'] ?? [];

				if (!$token) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'inputFiles');
				}
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
					if ($settings?->isWorkingFilesAsRefFiles()) {
						$dataRequest['referenceFiles'][] = [
							'id' => $token,
							'languages_ids' => $tasks,
						];
					}

					foreach ($tasks as $task) {
						if (empty($filesTaskMapping[$task])) {
							$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_WORKFILE] = [];
						}
						$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_WORKFILE][] = $token;
					}

					if (empty($tasks)) {
						foreach ($dataRequest['targetLanguages'] as $lang) {
							if (empty($filesTaskMapping[$lang])) {
								$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_WORKFILE] = [];
							}
							$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_WORKFILE][] = $token;
						}
					}
				}
			}
		}
		if (!empty($dataRequest['referenceFiles'])) {
			foreach ($dataRequest['referenceFiles'] as $fileData) {
				$token = $fileData['id'] ?? null;
				$tasks = $fileData['languages_ids'] ?? [];

				if (!$token) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'referenceFiles');
				}
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

					foreach ($tasks as $task) {
						if (empty($filesTaskMapping[$task])) {
							$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_REF] = [];
						}

						if (!in_array($token, $filesTaskMapping[$task])) {
							$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_REF][] = $token;
						}
					}

					if (empty($tasks)) {
						foreach ($dataRequest['targetLanguages'] as $lang) {
							if (empty($filesTaskMapping[$lang])) {
								$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_REF] = [];
							}
							$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_REF][] = $token;
						}
					}
				}
			}
		}

		$officePlace = $this->getOfficeCurrentUser();
		if (!empty($dataRequest['customer'])) {
			if (empty($officePlace)) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MANAGE_POLICY_EMPTY, ApiError::$descriptions[ApiError::CODE_MANAGE_POLICY_EMPTY]);
			}
			switch ($officePlace) {
				case SystemAccount::OFFICE_ONLY_RELATED:
				case SystemAccount::OFFICE_DEPARTMENT:
				case SystemAccount::OFFICE_OFFICE:
					if (strval($dataRequest['customer']) !== $customer->getId()) {
						return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
					}
					break;
				case SystemAccount::OFFICE_ALL_OFFICE:
					$customerIds = [];
					$customerPerson = $user->getCustomersPerson();
					if (!$customerPerson) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
					}
					$customers = $customerPerson->getCustomers();
					foreach ($customers as $cust) {
						$customerIds[] = $cust->getId();
					}
					if (!in_array(strval($dataRequest['customer']), $customerIds)) {
						return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
					}
					break;
				default:
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'manage_policy');
			}
			$customer = $this->customerRepo->find($dataRequest['customer']);

			if (!$customer) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
			}
		}

		if (!$customer->getUseDefaultCustomerServicesWorkflows()) {
			$customerService = $this->em->getRepository(CustomerService::class)->findOneBy(['customer' => $customer->getId(), 'service' => $dataRequest['service']]);

			if (!$customerService) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_service');
			}
		}
		$dataRequest['person'] = $user->getId();
		if (!in_array($officePlace, [SystemAccount::OFFICE_ONLY_RELATED, SystemAccount::OFFICE_DEPARTMENT])) {
			$dataRequest['office'] = $customer->getId();
		}
		unset($dataRequest['inputFiles'], $dataRequest['referenceFiles']);

		$customFields = [];

		if ($settings?->getCustomFields()) {
			foreach ($settings?->getCustomFields() as $customField) {
				if (isset($customField['name']) && isset($customField['value']) && $customField['enabled'] && !$customField['visible']) {
					$customFields[] = [
						'key' => $customField['name'],
						'value' => $customField['value'],
					];
				}
			}
		}

		$customFields[] = [
			'key' => 'source',
			'value' => 'AvantPortal',
		];

		if (!empty($dataRequest['customFields'])) {
			foreach ($dataRequest['customFields'] as $key => $value) {
				$customFields[] = [
					'key' => $key,
					'value' => $value,
				];
			}
		}

		$dataRequest['customFields'] = $customFields;

		$dataParams = $this->xtrfQuoteSrv->prepareCreateData($dataRequest);
		$projectCreateResponse = $this->clientConnector->createProject($dataParams);
		if (!$projectCreateResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		if ($settings && !empty($settings->getCategories())) {
			$params = [
				'macro_id' => self::CATEGORIES_MACRO_ID,
				'ids' => [$projectCreateResponse->getProjectDto()->id],
				'params' => [
					'projects' => [
						$projectCreateResponse->getProjectDto()->id => $settings->getCategories(),
					],
				],
			];

			$this->baseUtilsHandler->processMacro($params);
		}

		if (!empty($filesToUpload) && (!empty($filesToUpload['referenceFiles']) || !empty($filesToUpload['inputFiles']))) {
			$projectObjResponse = $this->xtrfConn->getProject($projectCreateResponse->getProjectDto()->id);
			$taskIdsFiles = [];
			$taskIds = [];
			if ($projectObjResponse->isSuccessfull()) {
				$taskList = $projectObjResponse->getProject()?->tasks;
				foreach ($taskList as $taskObj) {
					$targetLanguage = $taskObj['languageCombination']['targetLanguageId'] ?? null;
					$taskIds[] = $taskObj['id'];
					if ($targetLanguage && isset($filesTaskMapping[$targetLanguage])) {
						$taskIdsFiles[$taskObj['id']] = $filesTaskMapping[$targetLanguage];
					}
				}
			}
			$dataToSave = array_merge(
				[
					'entityId' => $projectCreateResponse->getProjectDto()->id,
					'EntityName' => CustomerportalFilesProjectsProcessCommand::TYPE_CP_PROJECT_EXTRA_FILES,
					'taskIds' => $taskIds,
					'owner' => $user->getId(),
					'copiedReferenceToWorking' => true,
					'tasksFilesMapping' => $taskIdsFiles,
				],
				$filesToUpload
			);
			$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($dataToSave));
		}

		$project = $projectCreateResponse->getProjectDto();

		// update projects instructions workaround for ticket #HELP-1084
		if (!empty($dataParams['notes'])) {
			$instructions = [
				'fromCustomer' => $dataParams['notes'],
			];
			$this->xtrfConn->additionalInstructions($project->id, $instructions);
		}

		$teamsWebhook = $customer->getSettings()->getTeamWebhook();
		if ($teamsWebhook) {
			$data = [
				'title' => "New project requested $project->idNumber",
				'message' => $project->service,
				'status' => TeamNotification::STATUS_SUCCESS,
				'date' => new \DateTime(), 'Y-m-d',
			];
			$this->notificationSrv->addNotification(
				NotificationService::NOTIFICATION_TYPE_TEAM,
				$teamsWebhook,
				$data
			);
		}

		return new ApiResponse(data: ['project' => $project]);
	}

	public function processSubmitExtraFiles(array $dataRequest): ApiResponse
	{
		$customer = $this->getCurrentCustomer();

		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$project = $this->projectRepository->find($dataRequest['projectId']);

		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$projectGetResponse = $this->xtrfConn->getProject($project->getId());
		if (!$projectGetResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		$filesToUpload = [
			'inputFiles' => [],
			'referenceFiles' => [],
		];

		$this->utilsSrv->arrayKeysToCamel($dataRequest);
		$filesTaskMapping = [];
		if (!empty($dataRequest['inputFiles'])) {
			foreach ($dataRequest['inputFiles'] as $fileData) {
				$token = $fileData['id'] ?? null;
				$tasks = $fileData['languages_ids'] ?? [];

				if (!$token) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'inputFiles');
				}
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

					foreach ($tasks as $task) {
						if (empty($filesTaskMapping[$task])) {
							$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_WORKFILE] = [];
						}
						$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_WORKFILE][] = $token;
					}

					if (empty($tasks)) {
						foreach ($filesTaskMapping as &$item) {
							$item[WorkflowJobFile::CATEGORY_WORKFILE][] = $token;
						}
					}
				}
			}
		}
		unset($dataRequest['inputFiles']);
		if (!empty($dataRequest['referenceFiles'])) {
			foreach ($dataRequest['referenceFiles'] as $fileData) {
				$token = $fileData['id'] ?? null;
				$tasks = $fileData['languages_ids'] ?? [];

				if (!$token) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_MISSING_PARAM, ApiError::$descriptions[ApiError::CODE_MISSING_PARAM], 'referenceFiles');
				}
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

					foreach ($tasks as $task) {
						if (empty($filesTaskMapping[$task])) {
							$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_REF] = [];
						}

						if (!in_array($token, $filesTaskMapping[$task])) {
							$filesTaskMapping[$task][WorkflowJobFile::CATEGORY_REF][] = $token;
						}
					}

					if (empty($tasks)) {
						foreach ($filesTaskMapping as &$item) {
							if (!in_array($token, $item)) {
								$item[WorkflowJobFile::CATEGORY_REF][] = $token;
							}
						}
					}
				}
			}
		}
		unset($dataRequest['referenceFiles']);

		if (!empty($filesToUpload) && (!empty($filesToUpload['referenceFiles']) || !empty($filesToUpload['inputFiles']))) {
			$taskIdsFiles = [];
			$taskList = $projectGetResponse->getProject()?->tasks;
			foreach ($taskList as $taskObj) {
				$targetLanguage = $taskObj['languageCombination']['targetLanguageId'] ?? null;
				if ($targetLanguage && isset($filesTaskMapping[$targetLanguage])) {
					$taskIdsFiles[$taskObj['id']] = $filesTaskMapping[$targetLanguage];
				}
			}
			$dataToSave = array_merge(
				[
					'entityId' => $project->getId(),
					'tasksFilesMapping' => $taskIdsFiles,
					'EntityName' => CustomerportalFilesProjectsProcessCommand::TYPE_CP_PROJECT_EXTRA_FILES,
				],
				$filesToUpload
			);
			$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($dataToSave));

			$data = [
				'subject' => 'AvantPortal: new action on project '.$project->getIdNumber().': Added new files',
				'template' => 'message',
				'data' => [
					'title' => 'Added new files',
					'message' => count($filesToUpload['inputFiles']).' working files and '.count($filesToUpload['referenceFiles']).' reference files.',
					'contact' => [
						'name' => $user->getName().' '.$user->getLastName(),
						'customer' => $this->getCurrentCustomer()?->getName(),
					],
					'project' => [
						'idNumber' => $project->getIdNumber(),
						'id' => $project->getId(),
					],
				],
			];

			$this->notificationSrv->addNotification(
				NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
				[
					$project->getProjectManager()->getEmail(),
					$project->getProjectCoordinator()?->getEmail(),
				],
				$data
			);

			return new ApiResponse(code: Response::HTTP_PARTIAL_CONTENT);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processAdditionalContactPerson(array $dataRequest): ApiResponse
	{
		$projectId = $dataRequest['id'];
		$contactsList = $dataRequest['contact_persons'];

		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$project = $this->projectRepository->find($projectId);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$projectCustomer = $project->getCustomer();
		if (!$projectCustomer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		$customerPersons = $projectCustomer->getContactPersons();
		$projectUserList = [];
		/** @var CustomerPerson $customerPerson */
		foreach ($customerPersons as $customerPerson) {
			$contactPerson = $customerPerson->getContactPerson();
			$projectUserList[] = $contactPerson->getId();
		}

		// Each contact person sent from client must to be into the project's customer child.
		if (!empty($projectUserList)) {
			foreach ($contactsList as $key => $contact) {
				if (!in_array($contact, $projectUserList)) {
					unset($contactsList[$key]);
				}
			}
		}

		if (empty($contactsList)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_EMPTY_LIST,
				ApiError::$descriptions[ApiError::CODE_EMPTY_LIST]
			);
		}

		$officeCheckerResponse = $this->checkOfficePermission($project);
		if ($officeCheckerResponse instanceof ErrorResponse) {
			return $officeCheckerResponse;
		}
		$data = [
			'primaryId' => $dataRequest['primary_id'],
			'additionalIds' => $contactsList,
		];

		if (isset($dataRequest['send_back_to_id'])) {
			$data['sendBackToId'] = $dataRequest['send_back_to_id'];
		}

		$response = $this->xtrfConn->additionalContactPerson($projectId, $data);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$result = [];
		foreach ($project->getCustomerPersons() as $contactPerson) {
			$result[] = [
				'id' => $contactPerson->getContactPerson()->getId(),
				'name' => $contactPerson->getContactPerson()->getName(),
				'lastName' => $contactPerson->getContactPerson()->getLastName(),
			];
		}

		$data = [
			'subject' => 'AvantPortal:  new action on project '.$project->getIdNumber().': Added new contact',
			'template' => 'message',
			'data' => [
				'title' => 'Added new contacts',
				'message' => null,
				'contact' => [
					'name' =>  $user->getName().' '.$user->getLastName(),
					'customer' => $this->getCurrentCustomer()?->getName(),
				],
				'project' =>  [
					'idNumber' => $project->getIdNumber(),
					'id' => $project->getId(),
				],
			],
		];

		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			[
				$project->getProjectManager()->getEmail(),
				$project->getProjectCoordinator()?->getEmail(),
			],
			$data
		);

		return new ApiResponse(data: $result);
	}

	public function processConfirmationFile(string $id)
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$response = $this->clientConnector->projectConfirmationFile($id);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$fileBinary = $response->getRaw();
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'confirmation_files');
		$filePath = $this->fileSystemSrv->filesPath.'/confirmation_files/confirmation'.uniqid().'.pdf';
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDownloadOutputFiles(string $id)
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}
		if ($project->getArchivedAt()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'project archived');
		}

		$response = $this->clientConnector->projectDownloadOutputFiles($id);

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$fileBinary = $response->getRaw();
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'project_temp_files');
		$filePath = $this->fileSystemSrv->filesPath.'/project_temp_files/zip_file'.uniqid().'.zip';
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDownloadFileById(string $id, string $fileId): ApiResponse|BinaryFileResponse
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}
		if ($project->getArchivedAt()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'project archived');
		}
		$response = $this->clientConnector->projectDownloadFileById($fileId);

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$fileBinary = $response->getRaw();
		$extension = '';
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'project_temp_files');
		$filePath = $this->fileSystemSrv->filesPath.'/project_temp_files/file'.uniqid();
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			$mimeType = mime_content_type($filePath);
			if (!empty($mimeType) && $this->utilsSrv->stringContains('/', $mimeType)) {
				$extension = explode('/', $mimeType)[1];
				rename($filePath, "$filePath.$extension");
			}
			$response = new BinaryFileResponse("$filePath.$extension");
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processGetFeedback(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$response = $this->clientConnector->getProjectFeedback($id);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		return new ApiResponse(data: $response->getRaw());
	}

	public function processGetArchive(string $id): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		/** @var Customer $customer */
		$customer = $this->getCurrentCustomer();

		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		if (null === $project->getArchivedAt() || empty($project->getArchivedProjectFile())) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$commonNotificationData = [
			'subject' => 'AvantPortal: new action on project '.$project->getIdNumber().': requested archived files',
			'template' => 'message',
			'data' => [
				'title' => 'Requested archived files',
				'contact' => [
					'name' => $user->getName().' '.$user->getLastName(),
					'customer' => $this->getCurrentCustomer()?->getName(),
				],
				'project' => [
					'idNumber' => $project->getIdNumber(),
					'id' => $project->getId(),
				],
			],
		];

		if (!$customer->getSettings()?->getProjectSettings()?->isDearchive()) {
			$commonNotificationData['data']['message'] = 'We just got your request. We will process it as soon as possible, and your project manager will get in touch with you.';

			$this->notificationSrv->addNotification(
				NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
				$user->getEmail(),
				$commonNotificationData
			);

			return new ApiResponse(code: Response::HTTP_NO_CONTENT);
		}

		$textToRemove1 = '/home/jboss/xtrf/xtrf_archive/';
		$textToRemove2 = '/home/jboss/xtrf/xtrf-archive/';
		$projectFileFullPath = $project->getArchivedProjectFile();
		$filename = basename($projectFileFullPath);
		$projectCleanPath1 = $this->utilsSrv->removeSubstringFromStart($textToRemove1, $projectFileFullPath);
		$projectCleanPath2 = $this->utilsSrv->removeSubstringFromStart($textToRemove2, $projectCleanPath1);
		$projectCleanPath = $this->utilsSrv->removeSubstringFromEnd("/$filename", $projectCleanPath2);
		$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_ARCHIVE);
		$filePubLink = $this->fileBucketService->getTemporaryUrl("$projectCleanPath/$filename");

		if (empty($filePubLink)) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_FILE_LINK_ERROR,
				ApiError::$descriptions[ApiError::CODE_FILE_LINK_ERROR]
			);
		}

		$commonNotificationData['data']['message'] = null;
		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			[
				$project->getProjectManager()?->getEmail(),
				$project->getProjectCoordinator()?->getEmail(),
			],
			$commonNotificationData
		);

		return new ApiResponse(data: ['public_link' => $filePubLink]);
	}

	public function processGetArchivePassword(string $id): ApiResponse
	{
		$project = $this->projectRepository->find($id);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		if (null === $project->getArchivedAt() || empty($project->getArchivedProjectFilePassword())) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		return new ApiResponse(
			data: [
				'password' => $project->getArchivedProjectFilePassword(),
			]
		);
	}

	public function processSubmitFeedback(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$projectId = $dataRequest['projectId'];
		$project = $this->projectRepository->find($projectId);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$data = $dataRequest['data'];
		if (!empty($dataRequest['comment'])) {
			$dataRequest['comment'] = strip_tags($dataRequest['comment'], RegexService::$htmlTagsAllowed);
		}
		$this->utilsSrv->arrayKeysToCamel($data);
		$response = $this->clientConnector->submitProjectFeedback($projectId, $data);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$data =  [
			'subject' => 'AvantPortal:  new action on project '.$project->getIdNumber().': Added new feedback',
			'template' => 'message',
			'data' => [
				'title' => 'New Feedback',
				'message' => isset($dataRequest['comment']) ? 'customer feedback comment: '.$dataRequest['comment'] : '',
				'contact' => [
					'name' =>  $user->getName().' '.$user->getLastName(),
					'customer' => $this->getCurrentCustomer()?->getName(),
				],
				'project' =>  [
					'idNumber' => $project->getIdNumber(),
					'id' => $project->getId(),
				],
			],
		];

		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			[
				$project->getProjectManager()->getEmail(),
				$project->getProjectCoordinator()?->getEmail(),
			],
			$data
		);

		return new ApiResponse(data: $response->getRaw());
	}

	public function processCancelProject(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$project = $this->projectRepository->find($dataRequest['projectId']);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$commonNotificationData = [
			'subject' => 'AvantPortal: new action on project '.$project->getIdNumber().': Request to cancel',
			'template' => 'message',
			'data' => [
				'title' => 'Request to cancel',
				'contact' => [
					'name' => $user->getName().' '.$user->getLastName(),
					'customer' => $this->getCurrentCustomer()->getName(),
				],
				'project' => [
					'idNumber' => $project->getIdNumber(),
					'id' => $project->getId(),
				],
			],
		];

		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			[
				$project->getProjectManager()->getEmail(),
				$project->getProjectCoordinator()?->getEmail(),
			],
			$commonNotificationData
		);

		$commonNotificationData['data']['message'] = 'We just got your request, we will process as soon as possible, and your project manager will get in touch with you';
		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			$user->getEmail(),
			$commonNotificationData
		);

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processSubmitComplaint(array $dataRequest): ApiResponse
	{
		$project = $this->projectRepository->find($dataRequest['id']);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		if (Project::STATUS_CLOSED !== $project->getStatus()) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'status'
			);
		}

		$type = $dataRequest['type'];
		if (FeedbackDto::TYPE_CUSTOMER_CLAIM !== $type) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'type'
			);
		}

		$feedbackDto = new FeedbackDto();
		$feedbackDto
			->setType($type)
			->setTargetLanguages($dataRequest['target_languages'])
			->setDescription(strip_tags($dataRequest['description'], RegexService::$htmlTagsAllowed));

		$response = $this->clientConnector->projectSubmitComplaint($dataRequest['id'], $feedbackDto);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function processUpdateCustomFields(string $projectId, array $customFields)
	{
		$count = 2;
		$customer = $this->getCurrentCustomer();
		$settings = $customer->getSettings();
		while ($count-- > 0) {
			$updateResponse = $this->xtrfConn->updateProjectCustomFields($projectId, $customFields);
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

	public function processAdditionalInstructions(array $dataRequest): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();

		$project = $this->projectRepository->find($dataRequest['id']);
		if (!$project) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}
		if (!empty($dataRequest['from_customer'])) {
			$now = new \DateTime();
			$preferences = $user->getPreferences();
			if (isset($preferences['timezone'])) {
				$now->setTimezone(new \DateTimeZone($preferences['timezone']));
			}
			$dataRequest['from_customer'] = sprintf(
				'Added by %s %s on %s:<br>%s <br><br>%s',
				$user->getName(),
				$user->getLastName(),
				$now->format('m/d/Y H:i:s'),
				strip_tags(
					$dataRequest['from_customer'],
					RegexService::$htmlTagsAllowed
				),
				$project->getCustomerSpecialInstructions()
			);
		}
		if (!empty($dataRequest['for_provider'])) {
			$dataRequest['for_provider'] = strip_tags(
				$dataRequest['for_provider'],
				RegexService::$htmlTagsAllowed
			);
		}
		if (!empty($dataRequest['internal'])) {
			$dataRequest['internal'] = strip_tags(
				$dataRequest['internal'],
				RegexService::$htmlTagsAllowed
			);
		}
		if (!empty($dataRequest['payment_note_for_customer'])) {
			$dataRequest['payment_note_for_customer'] = strip_tags(
				$dataRequest['payment_note_for_customer'],
				RegexService::$htmlTagsAllowed
			);
		}
		if (!empty($dataRequest['notes'])) {
			$dataRequest['notes'] = strip_tags(
				$dataRequest['notes'],
				RegexService::$htmlTagsAllowed
			);
		}
		unset($dataRequest['id']);
		$this->utilsSrv->arrayKeysToCamel($dataRequest);
		$response = $this->xtrfConn->additionalInstructions($project->getId(), $dataRequest);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR],
				$response->getErrorMessage()
			);
		}

		$data = [
			'subject' => 'AvantPortal: new action on project '.$project->getIdNumber().': Added new instructions',
			'template' => 'message',
			'data' => [
				'title' => 'Added new instructions',
				'message' => !empty($dataRequest['from_customer']) ? $dataRequest['from_customer'] : '',
				'contact' => [
					'name' => $user->getName().' '.$user->getLastName(),
					'customer' => $this->getCurrentCustomer()?->getName(),
				],
				'project' => [
					'idNumber' => $project->getIdNumber(),
					'id' => $project->getId(),
				],
			],
		];

		$this->notificationSrv->addNotification(
			NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
			[
				$project->getProjectManager()->getEmail(),
				$project->getProjectCoordinator()?->getEmail(),
			],
			$data
		);

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function prepareProjectSearchResponse(array $projectList): array
	{
		$result = [];
		/** @var Project $project */
		foreach ($projectList as $project) {
			$languagesCombinations = $project->getLanguagesCombinations();
			$startDate = $project->getStartDate()?->format(DateConstant::GLOBAL_FORMAT);
			$deadline = $project->getDeadline()?->format(DateConstant::GLOBAL_FORMAT);
			$deliveryDate = $project->getDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT);
			$closeDate = $project->getCloseDate()?->format(DateConstant::GLOBAL_FORMAT);
			$confirmationSentDate = $project->getConfirmationSentDate()?->format(DateConstant::GLOBAL_FORMAT);
			$service = $project->getService()?->getName();
			$specialization = $project->getSpecialization()?->getName();
			$currencyDto = null;

			if ($project->getCurrency() instanceof Currency) {
				$currencyDto = new CurrencyDto();
				$currencyDto
					->setName($project->getCurrency()->getName())
					->setSymbol($project->getCurrency()->getSymbol());
			}

			$projectStatus = strtolower($project->getStatus());
			if (Project::STATUS_COMPLAINT === $project->getStatus()) {
				foreach ($project->getTasks() as $task) {
					if ($task->getFeedbacks()) {
						foreach ($task->getFeedbacks() as $feedback) {
							if (in_array($feedback->getFeedbackType(), [Feedback::TYPE_CLIENT_APPROVAL, Feedback::TYPE_INTERNAL_NONCONFORMITY])) {
								$projectStatus = strtolower(Project::STATUS_CLOSED);
								break;
							}
						}
					}
				}
			}

			$percentage = $project->getTotalActivities() > 0 ? ($project->getProgressActivities() * 100) / $project->getTotalActivities() : 0;

			$projectProgress = [
				'total' => $project->getTotalActivities(),
				'percentage' => number_format($percentage, 2),
			];

			$projectManagerDto = null;
			if (null !== $project->getProjectManager() && $project->getProjectManager() instanceof User) {
				/** @var User $manager */
				$manager = $project->getProjectManager();
				$projectManagerDto = new GenericPersonDto(
					$manager->getId(),
					$manager->getFirstName(),
					$manager->getLastName(),
					$manager->getEmail(),
				);
			}

			$requestedByDto = null;
			if (null !== $project->getCustomerContactPerson()?->getContactPerson() && $project->getCustomerContactPerson()->getContactPerson() instanceof ContactPerson) {
				/** @var ContactPerson $requestedBy */
				$requestedBy = $project->getCustomerContactPerson()->getContactPerson();
				$requestedByDto = new GenericPersonDto(
					$requestedBy->getId(),
					$requestedBy->getName(),
					$requestedBy->getLastName(),
					$requestedBy->getEmail(),
				);
			}

			$sourceLanguageIds = [];

			$sourceLanguages = $targetLanguages = [];

			foreach ($languagesCombinations as $languagesCombination) {
				if (null !== $languagesCombination->getSourceLanguage() && $languagesCombination->getSourceLanguage() instanceof XtrfLanguage) {
					if (!in_array($languagesCombination->getSourceLanguage()->getId(), $sourceLanguageIds)) {
						$id = $languagesCombination->getSourceLanguage()->getId();
						$sourceLanguageIds[] = $id;
						$langSourceDto = new LanguageDto();
						$langSourceDto
							->setId($id)
							->setName($languagesCombination->getSourceLanguage()->getName())
							->setSymbol($languagesCombination->getSourceLanguage()->getSymbol());
						$sourceLanguages[] = $langSourceDto;
					}
				}
				if (null !== $languagesCombination->getTargetLanguage() && $languagesCombination->getTargetLanguage() instanceof XtrfLanguage) {
					$langtargetDto = new LanguageDto();
					$langtargetDto
						->setId($languagesCombination->getTargetLanguage()->getId())
						->setName($languagesCombination->getTargetLanguage()->getName())
						->setSymbol($languagesCombination->getTargetLanguage()->getSymbol());
					$targetLanguages[] = $langtargetDto;
				}
			}

			$tasksForReview = [];

			if (property_exists($project, 'tasksForReview') && !empty($project->tasksForReview)) {
				/** @var Task $task */
				foreach ($project->tasksForReview as $task) {
					$langSourceDto = null;
					if ($task->getSourceLanguage() instanceof XtrfLanguage) {
						$langSourceDto = new LanguageDto();
						$langSourceDto
							->setName($task->getSourceLanguage()->getName())
							->setSymbol($task->getSourceLanguage()->getSymbol());
					}
					$langTargetDto = null;
					if ($task->getTargetLanguage() instanceof XtrfLanguage) {
						$langTargetDto = new LanguageDto();
						$langTargetDto->setName($task->getTargetLanguage()->getName())
							->setSymbol($task->getTargetLanguage()->getSymbol());
					}

					$taskDtoData = new TaskDto(
						id: $task->getId(),
						activitiesStatus: $task->getActivitiesStatus(),
						actualStartDate: $task->getActualStartDate()?->format(DateConstant::GLOBAL_FORMAT),
						closeDate: $task->getCloseDate()?->format(DateConstant::GLOBAL_FORMAT),
						confirmedFilesDownloading: $task->getConfirmedFilesDownloading(),
						customerInvoiceId: $task->getCustomerInvoice()?->getId(),
						customerInvoiceNumber: $task->getCustomerInvoice()?->getFinalNumber(),
						deadline: $task->getDeadline()?->format(DateConstant::GLOBAL_FORMAT),
						deliveryDate: $deliveryDate,
						estimatedDeliveryDate: $task->getEstimatedDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT),
						finalInvoiceDate: $task->getFinalInvoiceDate()?->format(DateConstant::GLOBAL_FORMAT),
						invoiceable: $task->getInvoiceable(),
						ontimeStatus: $task->getOntimeStatus(),
						partialDeliveryDate: $task->getPartialDeliveryDate()?->format(DateConstant::GLOBAL_FORMAT),
						projectPhaseIdNumber: $task->getProjectPhaseIdNumber(),
						sourceLanguage: $langSourceDto,
						targetLanguage: $langTargetDto,
						status: Project::STATUS_REVIEW,
						totalAgreed: UtilsService::amountFormat($task->getTotalAgreed()),
						tmSavings: UtilsService::amountFormat($task->getTmSavings()),
						workingFilesNumber: $task->getWorkingFilesNumber(),
						progress: ['total' => $task->getTotalActivities(), 'percentage' => number_format($percentage, 2)],
						awaitingReview: $task->getTaskForReview()->count() > 0,
						forReview: $task->forReview,
					);

					$tasksForReview[] = $taskDtoData;
				}
			}

			/** @var Task $firstTask */
			$firstTask = $project->getTasks()->first();
			$projectInvoiceId = $projectInvoiceNumber = null;
			if ($firstTask) {
				$projectInvoiceId = $firstTask?->getCustomerInvoice()?->getId();
				$projectInvoiceNumber = $firstTask?->getCustomerInvoice()?->getFinalNumber();
			}

			$countAwaitingReview = $this->taskReviewRepository->countTaskForReview($project->getId());

			$projectDtoData = new ProjectDto(
				id: $project->getId(),
				idNumber: $project->getIdNumber(),
				refNumber: $project->getCustomerProjectNumber(),
				name: $project->getName(),
				totalAgreed: UtilsService::amountFormat($project->getTotalAgreed()),
				tmSavings: UtilsService::amountFormat($project->getTmSavings()),
				sourceLanguages: $sourceLanguages,
				targetLanguages: $targetLanguages,
				inputFiles: [],
				additionalContacts: [],
				startDate: $startDate,
				deadline: $deadline,
				deliveryDate: $deliveryDate,
				closeDate: $closeDate,
				status: $projectStatus,
				customerSpecialInstructions: $project->getCustomerSpecialInstructions(),
				costCenter: $project->getCostCenter(),
				currency: $currencyDto,
				confirmationSentDate: $confirmationSentDate,
				service: $service,
				specialization: $specialization,
				rapidFire: $project->getRapidFire(),
				rush: $project->getRush(),
				projectManager: $projectManagerDto,
				requestedBy: $requestedByDto,
				feedbacks: $project->getFeedbacks()->getValues(),
				office: $project->getCustomer()?->getName(),
				progress: $projectProgress,
				awaitingReview: $countAwaitingReview > 0,
				projectManagerProfilePic: null,
				accountManagerProfilePic: null,
				surveySent: null,
				archived: null,
				quoteId: null,
				invoiceId: $projectInvoiceId,
				invoiceNumber: $projectInvoiceNumber,
				tasksForReview: $tasksForReview,
				customFields: null,
				customer: [
					'id' => $project->getCustomer()->getId(),
					'name' => $project->getCustomer()->getName(),
				],
			);

			$result[] = $projectDtoData;
		}

		return $result;
	}
}
