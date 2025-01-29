<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Command\Services\BoostlingoFetchService;
use App\Command\Services\BoostlingoInvoiceCallService;
use App\Command\Services\BoostlingoRetrieveClientService;
use App\Command\Services\FilesQueueService;
use App\Command\Services\HubspotFetchService;
use App\Command\Services\HubspotProcessQueueService;
use App\Command\Services\InitializeLqaIssueTypeService;
use App\Command\Services\InitializeXtmLqaIssueTypeService;
use App\Command\Services\LinkAnalyticsProjectsService;
use App\Command\Services\LinkJobsService;
use App\Command\Services\PostmarkProcessQueueService;
use App\Command\Services\ProjectQuotesProcessQueueService;
use App\Command\Services\QuickBooksEntitiesUpdateService;
use App\Command\Services\StripeQueueService;
use App\Command\Services\TriggerSyncService;
use App\Command\Services\UpdateAllService;
use App\Command\Services\UpdateAnalyticsProjectsService;
use App\Command\Services\UpdateExtendedTableService;
use App\Command\Services\UpdateLqaService;
use App\Command\Services\UpdateMetricsService;
use App\Command\Services\UpdateStatisticsService;
use App\Command\Services\WorkflowAutoProcessQueueService;
use App\Command\Services\WorkflowProcessQueueService;
use App\Connector\Hubspot\HubspotConnector;
use App\Linker\Services\QboService;
use App\Linker\Services\RedisClients;
use App\Model\Entity\InternalUser;
use App\Model\Repository\CustomerRepository;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\ProjectRepository;
use App\Model\Repository\QuoteRepository;
use App\Model\Repository\TaskRepository;
use App\Model\Repository\WFHistoryRepository;
use App\Service\LoggerService;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Service\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommandHandler
{
	use UserResolver;

	private ?string $name = null;
	private LoggerService $loggerSrv;
	private ParameterBagInterface $paramBag;
	private TokenStorageInterface $tokenStorage;
	private QboService $qboService;
	private RedisClients $redisClients;
	private EntityManagerInterface $em;
	private LinkJobsService $linkJobSrv;
	private HubspotConnector $hsConnector;
	private UpdateLqaService $updateLqaSrv;
	private UpdateAllService $updateAllSrv;
	private FilesQueueService $filesProcessSrv;
	private StripeQueueService $stripeQueueSrv;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationSrv;
	private UpdateMetricsService $updateMetricSrv;
	private HubspotFetchService $updateHubspotSrv;
	private CustomerRepository $customerRepository;
	private UpdateStatisticsService $updateStatsSrv;
	private InternalUserRepository $internalUserRepo;
	private WorkflowProcessQueueService $wfProcessSrv;
	private WFHistoryRepository $historyRepository;
	private BoostlingoFetchService $boostlingoFetchSrv;
	private HubspotProcessQueueService $hubspotQueueSrv;
	private PostmarkProcessQueueService $postmarkQueueSrv;
	private BoostlingoInvoiceCallService $blInvoiceCallSrv;
	private LinkAnalyticsProjectsService $linkAnalyticProjSrv;
	private WorkflowAutoProcessQueueService $wfautoProcessSrv;
	private UpdateExtendedTableService $updateExtendedTableSrv;
	private BoostlingoRetrieveClientService $blRetriveClientSrv;
	private UpdateAnalyticsProjectsService $updateAnalyticProSrv;
	private InitializeLqaIssueTypeService $initializeLqaIssueTypeSrv;
	private ProjectQuotesProcessQueueService $projectQuotesProcessSrv;
	private QuickBooksEntitiesUpdateService $quickBooksEntitiesUpdateSrv;
	private InitializeXtmLqaIssueTypeService $initializeXtmLqaIssueTypeSrv;
	private TaskRepository $taskRepo;
	private QuoteRepository $quoteRepo;
	private ProjectRepository $projectRepo;
	private string $folderPath;
	private TriggerSyncService $triggerSyncSrv;
	private SecurityHandler $securityHandler;
	private RequestStack $requestStack;

	public function __construct(
		QboService $qboService,
		LoggerService $loggerSrv,
		TaskRepository $taskRepo,
		QuoteRepository $quoteRepo,
		RedisClients $redisClients,
		EntityManagerInterface $em,
		RequestStack $requestStack,
		LinkJobsService $linkJobSrv,
		HubspotConnector $hsConnector,
		UpdateLqaService $updateLqaSrv,
		ProjectRepository $projectRepo,
		UpdateAllService $updateAllSrv,
		ParameterBagInterface $paramBag,
		SecurityHandler $securityHandler,
		TriggerSyncService $triggerSyncSrv,
		FilesQueueService $filesProcessSrv,
		TokenStorageInterface $tokenStorage,
		StripeQueueService $stripeQueueSrv,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationSrv,
		UpdateMetricsService $updateMetricSrv,
		HubspotFetchService $updateHubspotSrv,
		CustomerRepository $customerRepository,
		UpdateStatisticsService $updateStatsSrv,
		InternalUserRepository $internalUserRepo,
		WorkflowProcessQueueService $wfProcessSrv,
		WFHistoryRepository $historyRepository,
		BoostlingoFetchService $boostlingoFetchSrv,
		HubspotProcessQueueService $hubspotQueueSrv,
		PostmarkProcessQueueService $postmarkQueueSrv,
		BoostlingoInvoiceCallService $blInvoiceCallSrv,
		LinkAnalyticsProjectsService $linkAnalyticProjSrv,
		WorkflowAutoProcessQueueService $wfautoProcessSrv,
		UpdateExtendedTableService $updateExtendedTableSrv,
		BoostlingoRetrieveClientService $blRetriveClientSrv,
		UpdateAnalyticsProjectsService $updateAnalyticProSrv,
		InitializeLqaIssueTypeService $initializeLqaIssueTypeSrv,
		ProjectQuotesProcessQueueService $projectQuotesProcessSrv,
		QuickBooksEntitiesUpdateService $quickBooksEntitiesUpdateSrv,
		InitializeXtmLqaIssueTypeService $initializeXtmLqaIssueTypeSrv,
	) {
		$this->em = $em;
		$this->taskRepo = $taskRepo;
		$this->paramBag = $paramBag;
		$this->loggerSrv = $loggerSrv;
		$this->qboService = $qboService;
		$this->linkJobSrv = $linkJobSrv;
		$this->hsConnector = $hsConnector;
		$this->tokenStorage = $tokenStorage;
		$this->redisClients = $redisClients;
		$this->updateLqaSrv = $updateLqaSrv;
		$this->updateAllSrv = $updateAllSrv;
		$this->wfProcessSrv = $wfProcessSrv;
		$this->parameterBag = $parameterBag;
		$this->updateStatsSrv = $updateStatsSrv;
		$this->stripeQueueSrv = $stripeQueueSrv;
		$this->filesProcessSrv = $filesProcessSrv;
		$this->notificationSrv = $notificationSrv;
		$this->updateMetricSrv = $updateMetricSrv;
		$this->hubspotQueueSrv = $hubspotQueueSrv;
		$this->updateHubspotSrv = $updateHubspotSrv;
		$this->postmarkQueueSrv = $postmarkQueueSrv;
		$this->internalUserRepo = $internalUserRepo;
		$this->blInvoiceCallSrv = $blInvoiceCallSrv;
		$this->wfautoProcessSrv = $wfautoProcessSrv;
		$this->historyRepository = $historyRepository;
		$this->customerRepository = $customerRepository;
		$this->boostlingoFetchSrv = $boostlingoFetchSrv;
		$this->blRetriveClientSrv = $blRetriveClientSrv;
		$this->linkAnalyticProjSrv = $linkAnalyticProjSrv;
		$this->updateAnalyticProSrv = $updateAnalyticProSrv;
		$this->updateExtendedTableSrv = $updateExtendedTableSrv;
		$this->projectQuotesProcessSrv = $projectQuotesProcessSrv;
		$this->initializeLqaIssueTypeSrv = $initializeLqaIssueTypeSrv;
		$this->quickBooksEntitiesUpdateSrv = $quickBooksEntitiesUpdateSrv;
		$this->initializeXtmLqaIssueTypeSrv = $initializeXtmLqaIssueTypeSrv;
		$this->quoteRepo = $quoteRepo;
		$this->projectRepo = $projectRepo;
		$this->folderPath = $this->paramBag->get('kernel.project_dir').'/src/Command/Command';
		$this->triggerSyncSrv = $triggerSyncSrv;
		$this->securityHandler = $securityHandler;
		$this->requestStack = $requestStack;
	}

	public function processList(): ApiResponse
	{
		$result = [];
		$finder = new Finder();
		foreach ($finder->in($this->folderPath) as $file) {
			$plainName = $file->getFilenameWithoutExtension();
			$filePath = "App\\Command\\Command\\$plainName";
			if (!property_exists($filePath, 'hidden')) {
				$ref = new \ReflectionClass($filePath);
				$constrParams = $ref->getConstructor()->getParameters();
				$params = [];
				foreach ($constrParams as $constrParam) {
					$prop = "{$constrParam->getName()}";
					$params[] = $this->$prop;
				}
				/** @var Command $command */
				$command = $ref->newInstanceArgs($params);

				$commandArguments = [];
				foreach ($command->getDefinition()->getOptions() as $option) {
					$commandArguments[] = [
						'name' => $option->getName(),
						'required' => $option->isValueRequired(),
					];
				}
				$result[] = [
					'name' => $plainName,
					'code' => $command->getName(),
					'description' => $command->getDescription(),
					'params' => $commandArguments,
				];
			}
		}

		return new ApiResponse(data: $result);
	}

	public function processRun(array $dataRequest): ApiResponse
	{
		/** @var InternalUser $user */
		$internalUser = $this->securityHandler->getCurrentUser($this->requestStack->getCurrentRequest());

		if (!$internalUser) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$commandName = $dataRequest['name'];
		$commandCode = $dataRequest['code'];
		$commandArguments = $dataRequest['params'];
		$filePath = "App\\Command\\Command\\$commandName";
		if (property_exists($filePath, 'hidden')) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'filepath');
		}
		$finder = new Finder();
		if (!$finder->in($this->folderPath)->files()->name("$commandName.php")->count()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'file');
		}
		$argumentsString = null;
		if (count($commandArguments)) {
			$argumentsString = '&'.http_build_query($commandArguments);
			$argumentsString = trim(str_replace('&', ' --', $argumentsString));
		}
		$queueId = uniqid('command_');
		$data = (object) [
			'id' => $queueId,
			'owner' => $internalUser,
			'code' => $commandCode,
			'arguments' => $argumentsString,
		];
		$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_COMMANDS_QUEUE, serialize($data));

		return new ApiResponse(data: [
			'queueId' => $queueId,
		]);
	}
}
