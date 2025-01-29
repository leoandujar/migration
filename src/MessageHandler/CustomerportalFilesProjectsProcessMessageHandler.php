<?php

namespace App\MessageHandler;

use App\Message\CustomerportalFilesProjectsProcessMessage;
use App\Model\Entity\Activity;
use App\Service\XtrfWebhookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Model\Entity\Task;
use App\Model\Entity\Quote;
use App\Service\LoggerService;
use App\Model\Entity\Project;
use App\Service\MercureService;
use App\Model\Entity\WorkflowJobFile;
use App\Connector\Xtrf\XtrfConnector;
use App\Linker\Services\RedisClients;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\Notification\NotificationService;
use App\Service\Notification\TeamNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CustomerportalFilesProjectsProcessMessageHandler
{
	public $hidden = true;
	public const TYPE_CP_QUOTES_EXTRA_FILES = 'quotes';
	public const TYPE_CP_PROJECT_EXTRA_FILES = 'projects';

	private string $redisKeyId;
	private ?string $queueName;
	private XtrfConnector $xtrfCon;
	private LoggerService $loggerSrv;
	private MercureService $mercureSrv;
	private RedisClients $redisClients;
	private NotificationService $notificationSrv;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;

	public function __construct(
		XtrfConnector $xtrfCon,
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		NotificationService $notificationSrv,
		MercureService $mercureSrv,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em,
	) {
		$this->xtrfCon = $xtrfCon;
		$this->loggerSrv = $loggerSrv;
		$this->mercureSrv = $mercureSrv;
		$this->redisClients = $redisClients;
		$this->fileBucketService = $fileBucketService;
		$this->notificationSrv = $notificationSrv;
		$this->em = $em;
	}

	public function __invoke(CustomerportalFilesProjectsProcessMessage $message): void
	{
		$data = $message->getData();
		$queueName = $message->getQueue();
		if (!$data) {
			$this->loggerSrv->addError('Project-Quotes Process Instance was called with empty data. Aborting.');

			return;
		}

		$this->redisKeyId = $data;
		$this->queueName = $queueName ?? null;
		$payload = $this->redisClients->redisMainDB->hmget(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, $data);
		if (!$payload) {
			$this->loggerSrv->addError("Project-Quotes Process Instance: Key $this->redisKeyId was not found in Redis. Aborting.");

			return;
		}
		if (is_array($payload)) {
			$payload = array_shift($payload);
		}

		if (($fileObj = unserialize($payload)) === false) {
			$this->loggerSrv->addError('Project-Quotes Process Instance unable to unserialize the data. Aborting.');

			return;
		}
		$data = json_decode(json_encode($fileObj));
		$this->loggerSrv->addInfo('PROJECT-QUOTE-DATA=>'.print_r($data, true));
		if (null === $data) {
			$this->loggerSrv->addError('Data could not be decoded. Aborting.');

			return;
		}

		$this->processEntity($data);
	}

	private function processEntity($object): void
	{
		try {
			$entityObject = null;
			$inputFiles = [];
			$referenceFiles = [];
			$entityType = $object->EntityName;
			$this->loggerSrv->addInfo("Processing $entityType with id {$object->entityId}");
			$this->loggerSrv->addInfo(sprintf('Input Files: %s, Reference Files: %s', json_encode($object->inputFiles), json_encode($object->referenceFiles)));
			switch ($object->EntityName) {
				case self::TYPE_CP_PROJECT_EXTRA_FILES:
					$entityObject = $this->xtrfCon->getProject($object->entityId);
					$entityObject = $entityObject?->getProject();
					$dBEntityObject = $this->em->getRepository(Project::class)->find($object->entityId);
					break;
				case self::TYPE_CP_QUOTES_EXTRA_FILES:
					$entityObject = $this->xtrfCon->getQuote($object->entityId);
					$dBEntityObject = $this->em->getRepository(Quote::class)->find($object->entityId);
					$entityObject = $entityObject?->getQuote();
					break;
				default:
					$this->loggerSrv->addError("Unrecognized file entity name {$object->entityName}");
			}

			if ($entityObject) {
				$customer = $dBEntityObject?->getCustomer();
				if ($customer) {
					$proSetting = $customer->getSettings()?->getProjectSettings();

					if ($proSetting?->isWorkingFilesAsRefFiles() && !isset($object->copiedReferenceToWorking)) {
						$object->referenceFiles = array_merge($object->referenceFiles, $object->inputFiles);
						$object->copiedReferenceToWorking = 1;
					}
				}
				$filesKeysTrace = [];
				$this->checkData($object->inputFiles, $inputFiles, $filesKeysTrace);
				$this->checkData($object->referenceFiles, $referenceFiles, $filesKeysTrace);
				$object->keyTrace = $filesKeysTrace;

				if (count($inputFiles) === count($object->inputFiles) && count($referenceFiles) === count($object->referenceFiles)) {
					$this->processExtraFiles([
						'inputFiles' => $inputFiles,
						'referenceFiles' => $referenceFiles,
					], $entityObject, $entityType, $object);
					$this->cleanAwaitingFiles(array_merge($object->inputFiles, $object->referenceFiles));
					$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_RULES_COMMAND_QUEUE, (array) serialize([
						'event' => XtrfWebhookService::EVENT_TASKS_FILES_READY,
						'object' => $object,
					]));
				} else {
					$this->redisClients->redisMainDB->rpush($customer->getSettings()?->getProjectSettings()->getFilesQueue(), serialize($object));
					if ($this->redisClients->redisMainDB->hexists(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, $this->redisKeyId)) {
						$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, [$this->redisKeyId]);
					}
				}
			}
		} catch (\Throwable $thr) {
			if (Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE !== $thr->getCode()) {
				$this->loggerSrv->addError('ERROR CREATING PROJECT-QUOTES IN XTRF', $thr);
				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_PROJECTS_QUOTES_ERROR, serialize($object));
				if ($this->redisClients->redisMainDB->hexists(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, $this->redisKeyId)) {
					$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, [$this->redisKeyId]);
				}
			} elseif (!empty($this->queueName)) {
				$this->redisClients->redisMainDB->rpush($this->queueName, serialize($object));
			}
		}
	}

	private function cleanAwaitingFiles(array $keys): void
	{
		foreach ($keys as $key) {
			$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_AWAITING_FILES, $key->Key);
		}
	}

	private function checkData(array $data, array &$result, array &$filesKeys): void
	{
		foreach ($data as $file) {
			if (is_string($file)) {
				$file = unserialize($file);
			}
			$decoded = $this->redisClients->redisMainDB->hmget(RedisClients::SESSION_KEY_AWAITING_FILES, $file->Key);
			if (is_array($decoded)) {
				$decoded = array_shift($decoded);
			}
			if (!$decoded) {
				break;
			}
			$decoded = unserialize($decoded);
			if (!empty($decoded->Token)) {
				$result[] = $decoded->Token;
				$filesKeys[$decoded->Token] = $file->Key;
			}
		}
	}

	private function processExtraFiles(array $data, ?object $entityObject, string $entityType, object &$originalData): void
	{
		$tokens = [];
		foreach ($data['inputFiles'] as $input) {
			$tokens[] = [
				'category' => WorkflowJobFile::CATEGORY_WORKFILE,
				'token' => $input,
			];
		}
		foreach ($data['referenceFiles'] as $reference) {
			$tokens[] = [
				'category' => WorkflowJobFile::CATEGORY_REF,
				'token' => $reference,
			];
		}

		$this->updateTaskFiles($tokens, $entityObject->tasks, $originalData, $entityObject->id, $entityType);
	}

	private function updateTaskFiles(array $tokenList, array $taskList, object &$originalData, $entityId, string $entityType): void
	{
		$indexedTokens = $this->prepareArrayIndex($originalData);
		$allFilesValid = true;
		$tokensToAddPending = [];
		$notificationSent = false;
		$iterationCount = 0;
		$reachedLimit = false;

		$projectSettings = null;
		$entityObj = null;

		if (CustomerportalFilesProjectsProcessMessageHandler::TYPE_CP_PROJECT_EXTRA_FILES === $entityType) {
			$entityObj = $this->projectRepo->find($entityId);
			if (!$entityObj) {
				$msg = "Project with id $entityId is not yet on replication.";
				$this->loggerSrv->addWarning($msg);
				throw new \Exception($msg);
			}
			$projectSettings = $entityObj->getCustomer()?->getSettings()?->getProjectSettings();
		}
		if (CustomerportalFilesProjectsProcessMessageHandler::TYPE_CP_QUOTES_EXTRA_FILES === $entityType) {
			$entityObj = $this->quoteRepo->find($entityId);
			if (!$entityObj) {
				$msg = "Quote with id $entityId is not yet on replication.";
				$this->loggerSrv->addWarning($msg);
				throw new \Exception($msg);
			}
			$projectSettings = $entityObj->getCustomer()?->getSettings()?->getProjectSettings();
		}

		$isDuplicateTask = $projectSettings?->isDuplicateTask() ?? false;

		$initialTaskIds = $originalData->taskIds ?? [];
		foreach ($taskList as $task) {
			$taskObj = $this->taskRepo->find($task['id']);
			$allowExtraCondition = true;
			/** @var Activity $firstActivity */
			$firstActivity = $taskObj?->getActivities()?->first();
			$isStarted = true;
			if ($firstActivity) {
				$isStarted = Activity::STATUS_ACCEPTED !== $firstActivity->getStatus()
					&& Activity::STATUS_OPENED !== $firstActivity->getStatus();
			}
			if (null !== $taskObj?->getWorkflow()?->getExternalSystemId() && $isStarted) {
				$allowExtraCondition = false;
				if ($isDuplicateTask) {
					try {
						$createTaskResponse = $this->createTask($taskObj, $entityObj, $entityId);
						if (!empty($createTaskResponse)) {
							$task = $createTaskResponse;
						}
					} catch (\Throwable) {
						$managerEmail = $entityObj->getProjectManager()?->getEmail();
						if (!$managerEmail) {
							$this->loggerSrv->addError("Unable to find project manager email for entity $entityType and id $entityId");
							continue;
						}
						$this->notificationSrv->addNotification(
							NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
							$managerEmail,
							[
								'subject' => 'Task could not be created.',
								'data' => [
									'name' => $entityObj->getProjectManager()?->getFirstName(),
									'title' => sprintf('AvantPortal: %s task could no be duplicated ', $task['id']),
									'content' => sprintf('AvantPortal: %s customer could no be duplicated the task %s', $entityObj->getCustomer()?->getId(), $task['id']),
									'details' => $task,
									'actionLink' => '',
									'actionButton' => '',
								],
							]
						);
					}
				}

				if (!$isDuplicateTask && !$notificationSent) {
					$publikLinks = [];
					foreach (array_merge($originalData->inputFiles, $originalData->referenceFiles) as $item) {
						$filePath = "avantportal/tasksFiles/{$task['id']}-{$item->FileName}";
						if ($this->fileBucketService->upload($filePath, $item->FilePath)) {
							$filePubLink = $this->fileBucketService->getTemporaryUrl($filePath);

							if (!$filePubLink) {
								$this->loggerSrv->addError("Unable to generate public link for entity $entityType and id $entityId");
								continue;
							}

							$publikLinks[] = $filePubLink;
						}
					}

					$managerEmail = $entityObj->getProjectManager()?->getEmail();
					if (!$managerEmail) {
						$this->loggerSrv->addError("Unable to find project manager email for entity $entityType and id $entityId");
						continue;
					}
					$projectId = $entityObj->getIdNumber();
					$subject = sprintf('AvantPortal: %s customer added new files ', $projectId);
					$content = 'The customer added new files to the project that is using XTM or other connector which requires to add the files manually';
					$customerName = $entityObj->getCustomer()?->getName();
					$details = [
						"Customer: $customerName",
						"Project Id: $projectId",
					];

					$this->notificationSrv->addNotification(
						NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
						$managerEmail,
						[
							'subject' => $subject,
							'data' => [
								'name' => $entityObj->getProjectManager()?->getFirstName(),
								'title' => 'Additional Files',
								'content' => $content,
								'details' => $details,
								'actionLinks' => $publikLinks,
							],
						]
					);

					$notificationSent = true;
				}
			}
			if ($allowExtraCondition) {
				$tasksFilesMap = [];
				if (isset($originalData->tasksFilesMap)) {
					$tasksFilesMap = json_decode(json_encode($originalData->tasksFilesMap), true);
				}
				if (!empty($initialTaskIds) && !in_array($task['id'], $initialTaskIds)) {
					continue;
				}
				foreach ($tokenList as $token) {
					$success = true;
					$category = $token['category'] ?? WorkflowJobFile::CATEGORY_REF;
					$token = $token['token'] ?? $token;
					$data = [
						'token' => $token,
						'category' => $category,
					];
					$shortFileId = '';
					if (isset($originalData->keyTrace[$token])) {
						$shortFileId = $originalData->keyTrace[$token];
					}

					if ($shortFileId && isset($tasksFilesMap[$task['id']][$category]) && in_array($shortFileId, $tasksFilesMap[$task['id']][$category])) {
						continue;
					}
					$strictTaskMapFiles = $originalData->tasksFilesMapping ?? [];
					$strictTaskMapFiles = json_decode(json_encode($strictTaskMapFiles), true);
					if (!isset($strictTaskMapFiles[$task['id']][$category]) || (!empty($strictTaskMapFiles) && !in_array($shortFileId, $strictTaskMapFiles[$task['id']][$category]))) {
						$this->loggerSrv->addInfo("File $shortFileId was not configured for task id =>".$task['id']);
						continue;
					}
					$response = $this->xtrfCon->uploadTaskFile(strval($task['id']), $data);
					if (!$response->isSuccessfull()) {
						$allFilesValid = false;
						if (isset($indexedTokens[$token])) {
							$tokensToAddPending[$indexedTokens[$token]->Key] = $indexedTokens[$token];
						} elseif (isset($originalData->keyTrace[$token])) {
							$tokensToAddPending[$originalData->keyTrace[$token]] = $indexedTokens[$originalData->keyTrace[$token]];
						}
						$success = false;
					} else {
						$this->loggerSrv->addInfo("Added file to task {$task['id']}", $data);
					}
					if ($shortFileId) {
						if ($success) {
							$tasksFilesMap[$task['id']][$category][] = $shortFileId;
							$originalData->tasksFilesMap = $tasksFilesMap;
						}
						if (10 === ++$iterationCount) {
							$reachedLimit = true;
							break 2;
						}
					}
				}
			}
		}

		$uniqueData = array_unique(array_keys($tokensToAddPending));
		foreach ($uniqueData as $token) {
			$item = $tokensToAddPending[$token];
			$item->Token = null;
			$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_PENDING_FILES, [$token => serialize($item)]);
			$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, [$token => microtime(true)]);
			$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_AWAITING_FILES, $token);
			$this->loggerSrv->addInfo('File removed from Awaiting and added to Pending again', ['item' => $item]);
		}

		if (!$allFilesValid) {
			throw new \Exception('not all files processed');
		} else {
			if ($reachedLimit) {
				$msg = "File Task processing for project $entityId reach limit of 10 iterations. Enqueueing again.";
				$this->loggerSrv->addNotice($msg);
				throw new \Exception($msg, Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);
			}
			$msg = "All files for $entityType $entityId have been uploaded.";
			$this->loggerSrv->addNotice($msg);
			$autostart = $projectSettings?->isAutostart() ?? false;
			if ($autostart) {
				$this->loggerSrv->addInfo("Autostart project $entityId");
				foreach ($taskList as $task) {
					$response = $this->xtrfCon->startTask(strval($task['id']));
					if (null !== $response && $response->isSuccessfull()) {
						$this->loggerSrv->addInfo("Task {$task['id']} - {$task['idNumber']} started");
					} else {
						$this->loggerSrv->addWarning("Task {$task['id']} - {$task['idNumber']} {$response->getErrorMessage()}");
						$teamsWebhook = $entityObj->getCustomer()?->getSettings()?->getTeamWebhook() ?? false;
						if ($teamsWebhook) {
							$data = [
								'title' => "Failed to autostart task {$task['idNumber']}",
								'message' => $response->getErrorMessage(),
								'status' => TeamNotification::STATUS_FAILURE,
							];
							$this->notificationSrv->addNotification(
								NotificationService::NOTIFICATION_TYPE_TEAM,
								$teamsWebhook,
								$data
							);
						}
					}
				}
			}

			try {
				$this->mercureSrv->publish([
					'entityType' => $entityType,
					'entityId' => $entityId,
					'IdNumber' => $entityObj->getIdNumber(),
					'status' => MercureService::STATUS_SUCCESS,
				], $originalData->owner);
			} catch (\Throwable $thr) {
				$this->loggerSrv->addWarning("Mercure error while processing QUOTE-PROJECT $entityId queue.", $thr);
			}
		}
	}

	private function createTask(Task $task, Quote|Project $entity, $entityId): mixed
	{
		$dataCreate = [
			'specializationId' => $entity->getSpecialization()?->getId(),
			'workflowId' => $entity->getWorkflow()?->getId(),
			'name' => $entity->getName(),
			'languageCombination' => [
				'sourceLanguageId' => $task->getSourceLanguage()?->getId(),
				'targetLanguageId' => $task->getTargetLanguage()?->getId(),
			],
			'dates' => [
				'startDate' => ['time' => (new \DateTime('now'))->getTimestamp() * 1000],
			],
		];

		$createResponse = $this->xtrfCon->createAdditionalTaskRequest($entityId, $dataCreate);
		if (!$createResponse->isSuccessfull()) {
			$this->loggerSrv->addCritical("Unable to create duplicate task for entity=>$entityId");

			return null;
		}

		return $createResponse->getRaw();
	}

	private function prepareArrayIndex(object $originalData): array
	{
		$indexedTokens = [];
		foreach (array_merge($originalData->inputFiles, $originalData->referenceFiles) as $inputFile) {
			$indexedTokens[$inputFile->Token] = $inputFile;
			$indexedTokens[$inputFile->Key] = $inputFile;
		}

		return $indexedTokens;
	}
}
