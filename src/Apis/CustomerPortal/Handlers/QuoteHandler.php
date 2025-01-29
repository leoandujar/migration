<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\UtilsHandler as BaseUtilsHandler;
use App\Connector\Xtrf\XtrfConnector;
use App\Model\Entity\CustomFieldConfiguration;
use App\Model\Entity\SystemAccount;
use App\Model\Entity\Task;
use App\Model\Entity\Quote;
use App\Service\RegexService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Model\Entity\WorkflowJobFile;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Util\UtilsService;
use App\Linker\Services\RedisClients;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Repository\QuoteRepository;
use App\Service\FileSystem\FileSystemService;
use App\Connector\ApacheTika\TikaConnector;
use App\Model\Repository\CustomerRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Model\Repository\TaskReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Model\Repository\LanguageTagRepository;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use App\Command\Command\CustomerportalFilesProjectsProcessCommand;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Apis\Shared\Handlers\BaseHandler;
use App\Model\Repository\CustomerPriceListLanguageCombinationRepository;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Util\Factory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class QuoteHandler extends BaseHandler
{
	private const TARGET_INPUT_FILE = 1;
	private const TARGET_REF_FILE = 2;
	private const REJECT_MACRO_ID = 283;
	private const CATEGORIES_MACRO_ID = 296;

	private UtilsService $utilsSrv;
	private SessionInterface $session;
	private TikaConnector $tikaConnector;
	private RedisClients $redisClients;
	private XtrfQuoteService $xtrfQuoteSrv;
	private QuoteRepository $quoteRepository;
	private FileSystemService $fileSystemSrv;
	private TokenStorageInterface $tokenStorage;
	private CustomerRepository $customerRepository;
	private CustomerPortalConnector $clientConnector;
	private TaskReviewRepository $taskReviewRepository;
	private LanguageTagRepository $sourceLanguageRepository;
	private ContactPersonRepository $contactPersonRepository;
	private CustomerPriceListLanguageCombinationRepository $priceListlangCombRepository;
	private XtrfConnector $xtrfConn;
	private RequestStack $requestStack;
	private BaseUtilsHandler $baseUtilsHandler;
	private EntityManagerInterface $em;

	public function __construct(
		RequestStack $requestStack,
		UtilsService $utilsSrv,
		XtrfQuoteService $xtrfQuoteSrv,
		TokenStorageInterface $tokenStorage,
		RedisClients $redisClients,
		FileSystemService $fileSystemSrv,
		CustomerPortalConnector $clientConnector,
		QuoteRepository $quoteRepository,
		CustomerRepository $customerRepository,
		TaskReviewRepository $taskReviewRepository,
		ContactPersonRepository $contactPersonRepository,
		LanguageTagRepository $sourceLanguageRepository,
		CustomerPriceListLanguageCombinationRepository $priceListlangCombRepository,
		XtrfConnector $xtrfConn,
		TikaConnector $tikaConnector,
		BaseUtilsHandler $baseUtilsHandler,
		EntityManagerInterface $em
	) {
		parent::__construct($requestStack, $em);
		$this->session = $requestStack->getSession();
		$this->utilsSrv = $utilsSrv;
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->tokenStorage = $tokenStorage;
		$this->redisClients = $redisClients;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->clientConnector = $clientConnector;
		$this->quoteRepository = $quoteRepository;
		$this->customerRepository = $customerRepository;
		$this->taskReviewRepository = $taskReviewRepository;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->sourceLanguageRepository = $sourceLanguageRepository;
		$this->priceListlangCombRepository = $priceListlangCombRepository;
		$this->tikaConnector = $tikaConnector;
		$this->xtrfConn = $xtrfConn;
		$this->requestStack = $requestStack;
		$this->baseUtilsHandler = $baseUtilsHandler;
		$this->em = $em;
	}

	public function processGetQuotes(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();
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
					if (empty($dataRequest['requested_by']) || in_array($id, $dataRequest['requested_by'], true)) {
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
					if (empty($dataRequest['offices']) || in_array($cust->getId(), $dataRequest['offices'], true)) {
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
					if (empty($dataRequest['offices']) || in_array($cust->getId(), $dataRequest['offices'], true)) {
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

		$totalRows = $this->quoteRepository->getCountSearchQuote($dataRequest);
		$paginationDto = new PaginationDto($dataRequest['page'], $dataRequest['per_page'], $totalRows, $dataRequest['sort_order'], $dataRequest['sort_by']);
		$dataRequest['start'] = $paginationDto->from;
		unset($dataRequest['requested_by']);
		$sqlResponse = $this->quoteRepository->getSearchQuote($dataRequest);

		$result = [];
		foreach ($sqlResponse as $quote) {
			$data['countAwaitingReview'] = $this->taskReviewRepository->countTaskForReview($quote->getId());

			$result[] = Factory::quoteDtoInstance($quote, $data);
		}

		$response = new DefaultPaginationResponse(
			[
				'entities' => $result,
			]
		);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processExportQuotes(array $dataRequest): BinaryFileResponse|ApiResponse
	{
		$quotesResponse = $this->processGetQuotes($dataRequest);
		if ($quotesResponse instanceof ErrorResponse) {
			return $quotesResponse;
		}

		$arrayData = json_decode($quotesResponse->getContent(), true);
		$projects = $arrayData['data'];
		if (empty($projects)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$flattenedData = array_map(function ($item) {
			return $this->flatten($item);
		}, $arrayData['data']);

		$serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
		$csvData = $serializer->encode($flattenedData, 'csv');

		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'quote_files');
		$filePath = $this->fileSystemSrv->filesPath.'/quote_files/quote_info_'.uniqid().'.csv';
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

	public function processGetQuote(string $id): ApiResponse
	{
		$quote = $this->quoteRepository->find($id);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		$officeCheckerResponse = $this->checkOfficePermission($quote);
		if ($officeCheckerResponse instanceof ErrorResponse) {
			return $officeCheckerResponse;
		}

		$accountManagerPic = $quote->getAccountManager()?->getEntityImage();
		$projectManagerPic = $quote?->getProjectManager()?->getEntityImage();
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

		$data['accountManagerPicData'] = $accountManagerPicData;
		$data['projectManagerPicData'] = $projectManagerPicData;
		$data['countAwaitingReview'] = $this->taskReviewRepository->countTaskForReview($quote->getId());

		/** @var Task $tasks */
		$tasks = $quote->getTasks();
		if (null !== $tasks && $tasks->count() > 0) {
			$taskDto = [];
			foreach ($tasks as $task) {
				if ($task instanceof Task) {
					$taskDto[] = Factory::taskDtoInstance($task);
				}
			}
		}

		$quoteDto  = Factory::quoteDtoInstance($quote, $data);

		return new ApiResponse(
			[
				'quote' => $quoteDto,
				'tasks' => $taskDto,
			]
		);
	}

	public function processAddAdditionalTask(array $dataRequest): ApiResponse
	{
		$quote = $this->quoteRepository->find($dataRequest['quoteId']);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		/** @var Task $task */
		$task = $quote->getTasks()->first();

		if (!$task) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'task');
		}

		$dataCreate = [
			'specializationId' => $quote->getspecialization()?->getId(),
			'workflowId' => $quote->getWorkflow()?->getId(),
			'name' => $quote->getName(),
			'languageCombination' => [
				'sourceLanguageId' => $dataRequest['source_language'],
				'targetLanguageId' => $dataRequest['target_language'],
			],
			'dates' => [
				'startDate' => ['time' => (new \DateTime('now'))->getTimestamp() * 1000],
			],
		];

		$createResponse = $this->xtrfConn->createAdditionalTaskRequest($quote->getId(), $dataCreate);

		if (!$createResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processConfirmationFile(string $quoteId): BinaryFileResponse|ApiResponse
	{
		$quote = $this->quoteRepository->find($quoteId);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}
		$response = $this->clientConnector->quoteConfirmationFile($quoteId);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
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

	public function processCreate(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		if (!empty($dataRequest['notes'])) {
			$dataRequest['notes'] = strip_tags($dataRequest['notes'], RegexService::$htmlTagsAllowed);
		}

		$customer = $this->getCurrentCustomer();
		$officePlace = $this->getOfficeCurrentUser();
		if (!empty($dataRequest['customer'])) {
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
					if (strval($dataRequest['customer']) !== $customer->getId()) {
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
					if (!in_array(strval($dataRequest['customer']), $customerIds, true)) {
						return new ErrorResponse(
							Response::HTTP_FORBIDDEN,
							ApiError::CODE_NOT_ENOUGH_PERMISSIONS,
							ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]
						);
					}
					break;
				case SystemAccount::OFFICE_ALL_OFFICE_RELATED:
					if (strval($dataRequest['customer']) !== $customer->getId()) {
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
					if (!in_array(strval($dataRequest['customer']), $customerIds, true)) {
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
			$dataRequest['office'] = $dataRequest['customer'];
		} else {
			$dataRequest['office'] = $customer->getId();
		}

		$filesToUpload = [
			'inputFiles' => [],
			'referenceFiles' => [],
		];
		$settings = $customer->getSettings()?->getProjectSettings();
		$filesTaskMapping = [];
		if (!empty($dataRequest['input_files'])) {
			foreach ($dataRequest['input_files'] as $fileData) {
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
						$dataRequest['reference_files'][] = [
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
						foreach ($dataRequest['target_languages'] as $lang) {
							if (empty($filesTaskMapping[$lang])) {
								$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_WORKFILE] = [];
							}
							$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_WORKFILE][] = $token;
						}
					}
				}
			}
		}
		if (!empty($dataRequest['reference_files'])) {
			foreach ($dataRequest['input_files'] as $fileData) {
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
						foreach ($dataRequest['target_languages'] as $lang) {
							if (empty($filesTaskMapping[$lang])) {
								$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_REF] = [];
							}
							$filesTaskMapping[$lang][WorkflowJobFile::CATEGORY_REF][] = $token;
						}
					}
				}
			}
		}
		unset($dataRequest['input_files'], $dataRequest['reference_files']);
		$dataRequest['person'] = $user->getId();
		if ($officePlace && (SystemAccount::OFFICE_ONLY_RELATED === $officePlace || SystemAccount::OFFICE_DEPARTMENT === $officePlace)) {
			unset($dataRequest['office']);
		}

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

		if (!empty($dataRequest['custom_fields'])) {
			foreach ($dataRequest['custom_fields'] as $key => $value) {
				$customFields[] = [
					'key' => $key,
					'value' => $value,
				];
			}
		}

		$dataRequest['custom_fields'] = $customFields;
		$dataParams = $this->xtrfQuoteSrv->prepareCreateData($dataRequest);
		if (
			empty($dataParams['officeId'])
			&& ($officePlace
				&& (SystemAccount::OFFICE_ONLY_RELATED !== $officePlace
					&& SystemAccount::OFFICE_DEPARTMENT !== $officePlace))) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
				'office'
			);
		}
		$quoteCreateResponse = $this->clientConnector->createQuote($dataParams);
		if (!$quoteCreateResponse->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		if ($settings && !empty($settings->getCategories())) {
			$params = [
				'macro_id' => self::CATEGORIES_MACRO_ID,
				'ids' => [$quoteCreateResponse->getQuoteDto()->id],
				'params' => [
					'projects' => [
						$quoteCreateResponse->getQuoteDto()->id => $settings->getCategories(),
					],
				],
			];

			$this->baseUtilsHandler->processMacro($params);
		}

		if (!empty($filesToUpload) && (!empty($filesToUpload['referenceFiles']) || !empty($filesToUpload['inputFiles']))) {
			$quoteObjResponse = $this->xtrfConn->getQuote($quoteCreateResponse->getQuoteDto()->id);
			$taskIdsFiles = [];
			$taskIds = [];
			if ($quoteObjResponse->isSuccessfull()) {
				$taskList = $quoteObjResponse->getQuote()?->tasks;
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
					'entityId' => $quoteCreateResponse->getQuoteDto()->id,
					'EntityName' => CustomerportalFilesProjectsProcessCommand::TYPE_CP_QUOTES_EXTRA_FILES,
					'owner' => $user->getId(),
					'taskIds' => $taskIds,
					'copiedReferenceToWorking' => true,
					'tasksFilesMapping' => $taskIdsFiles,
				],
				$filesToUpload
			);
			$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($dataToSave));

			return new ApiResponse(
				code: Response::HTTP_PARTIAL_CONTENT,
				data: ['quote' => $quoteCreateResponse->getQuoteDto()]
			);
		}

		return new ApiResponse(data: ['quote' => $quoteCreateResponse->getQuoteDto()]);
	}

	public function processUpdate(array $dataRequest): ApiResponse
	{
		$quote = $this->quoteRepository->find($dataRequest['quoteId']);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		$officeCheckerResponse = $this->checkOfficePermission($quote);
		if ($officeCheckerResponse instanceof ErrorResponse) {
			return $officeCheckerResponse;
		}

		$customFields = [];

		if (!empty($dataRequest['custom_fields'])) {
			foreach ($dataRequest['custom_fields'] as $key => $value) {
				$customField = $this->em->getRepository(CustomFieldConfiguration::class)->findOneBy(['key' => $key]);
				if (!$customField) {
					continue;
				}
				$customFields[] = [
					'key' => $key,
					'value' => $value,
					'name' => $customField->getName(),
				];
			}
			$quoteCustomFieldsResponse = $this->xtrfConn->updateQuoteCustomFields($dataRequest['quoteId'], $customFields);
			if (!$quoteCustomFieldsResponse->isSuccessfull()) {
				return new ErrorResponse(
					Response::HTTP_BAD_GATEWAY,
					ApiError::CODE_XTRF_COMMUNICATION_ERROR,
					ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
				);
			}
		}

		if (isset($dataRequest['instructions'])) {
			$instructions = [
				'fromCustomer' => $dataRequest['instructions'],
			];
			$quoteCustomFieldsResponse = $this->xtrfConn->updateQuoteInstructions($dataRequest['quoteId'], $instructions);
			if (!$quoteCustomFieldsResponse->isSuccessfull()) {
				return new ErrorResponse(
					Response::HTTP_BAD_GATEWAY,
					ApiError::CODE_XTRF_COMMUNICATION_ERROR,
					ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
				);
			}
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processSubmitExtraFiles(array $dataRequest): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		$quote = $this->quoteRepository->find($dataRequest['quoteId']);

		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$quoteGetResponse = $this->xtrfConn->getQuote($quote->getId());
		if (!$quoteGetResponse->isSuccessfull()) {
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
			$taskList = $quoteGetResponse->getQuote()?->tasks;
			foreach ($taskList as $taskObj) {
				$targetLanguage = $taskObj['languageCombination']['targetLanguageId'] ?? null;
				if ($targetLanguage && isset($filesTaskMapping[$targetLanguage])) {
					$taskIdsFiles[$taskObj['id']] = $filesTaskMapping[$targetLanguage];
				}
			}
			$dataToSave = array_merge(
				[
					'entityId' => $quote->getId(),
					'tasksFilesMapping' => $taskIdsFiles,
					'EntityName' => CustomerportalFilesProjectsProcessCommand::TYPE_CP_QUOTES_EXTRA_FILES,
				],
				$filesToUpload
			);
			$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($dataToSave));

			return new ApiResponse(code: Response::HTTP_PARTIAL_CONTENT);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processDownloadFileById(string $id, string $fileId): ApiResponse|BinaryFileResponse
	{
		$quote = $this->quoteRepository->find($id);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		$response = $this->clientConnector->projectDownloadFileById($fileId);

		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
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

	public function processApprove(string $quoteId): ApiResponse
	{
		$quote = $this->quoteRepository->find($quoteId);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		if (Quote::STATUS_SENT !== $quote->getStatus()) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'status'
			);
		}

		$response = $this->clientConnector->quoteAccept($quoteId);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processReject(array $dataRequest): ApiResponse
	{
		$quote = $this->quoteRepository->find($dataRequest['id']);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		if (Quote::STATUS_SENT !== $quote->getStatus()) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'status'
			);
		}

		$params = [
			'macro_id' => self::REJECT_MACRO_ID,
			'ids' => [$dataRequest['id']],
			'params' => [
				'quotes' => [
					$dataRequest['id'] => [
						'reasonId' => $dataRequest['reason_id'],
						'comment' => $dataRequest['comment'] ?? '',
					],
				],
			],
		];

		$macro = $this->baseUtilsHandler->processMacro($params);
		$response = json_decode($macro->getContent());
		if (!isset($response->data)) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_ERROR,
				ApiError::$descriptions[ApiError::CODE_MACRO_RUN_ERROR]
			);
		}

		$quotes = $response?->data?->quotes;

		return new ApiResponse(
			data: $quotes[0],
		);
	}

	public function processAcceptDecline(array $dataRequest): ApiResponse
	{
		$quote = $this->quoteRepository->find($dataRequest['id']);
		if (!$quote) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'quote');
		}

		if (Quote::STATUS_SENT !== $quote->getStatus()) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'status'
			);
		}

		$action = $dataRequest['action'];
		if (!in_array($action, [Quote::ACTION_ACCEPT, Quote::ACTION_REJECT], true)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'action'
			);
		}

		$response = $this->clientConnector->quoteAcceptDecline(
			$dataRequest['id'],
			['action' => $action]
		);
		if (!$response->isSuccessfull()) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_XTRF_COMMUNICATION_ERROR,
				ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]
			);
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
