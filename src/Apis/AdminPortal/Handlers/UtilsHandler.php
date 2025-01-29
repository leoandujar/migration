<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Command\Command\CustomerportalFilesProjectsProcessCommand;
use App\Connector\ApacheTika\TikaConnector;
use App\Linker\Services\RedisClients;
use App\Model\Entity\InternalUser;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Util\PostmarkService;
use Doctrine\ORM\Mapping\MappingException;
use App\Service\Notification\TeamNotification;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Notification\NotificationService;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Connector\Cloudflared\CloudflaredConnector;
use App\Connector\XtrfMacro\MacroConnector;
use App\Apis\Shared\Handlers\UtilsHandler as BaseUtilsHandler;

class UtilsHandler
{
	use UserResolver;

	private EntityManagerInterface $em;
	private PostmarkService $postmarkSrv;
	private TokenStorageInterface $tokenStorage;
	private NotificationService $notificationSrv;
	private ContactPersonRepository $contactPersonRepo;
	private CloudflaredConnector $cloudflaredConnector;
	private MacroConnector $macroConn;
	private CloudFileSystemService $fileSystemSrv;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private TikaConnector $tikaConnector;
	private SecurityHandler $securityHandler;
	private RequestStack $requestStack;
	private BaseUtilsHandler $baseUtilsHandler;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		TokenStorageInterface $tokenStorage,
		ContactPersonRepository $contactPersonRepo,
		NotificationService $notificationSrv,
		PostmarkService $postmarkSrv,
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		RequestStack $requestStack,
		RedisClients $redisClients,
		TikaConnector $tikaConnector,
		SecurityHandler $securityHandler,
		CloudflaredConnector $cloudflaredConnector,
		MacroConnector $macroConn,
		CloudFileSystemService $fileSystemSrv,
		BaseUtilsHandler $baseUtilsHandler,
		ParameterBagInterface $parameterBag,
	) {
		$this->em = $em;
		$this->tokenStorage = $tokenStorage;
		$this->notificationSrv = $notificationSrv;
		$this->postmarkSrv = $postmarkSrv;
		$this->contactPersonRepo = $contactPersonRepo;
		$this->cloudflaredConnector = $cloudflaredConnector;
		$this->macroConn = $macroConn;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->tikaConnector = $tikaConnector;
		$this->securityHandler = $securityHandler;
		$this->requestStack = $requestStack;
		$this->baseUtilsHandler = $baseUtilsHandler;
		$this->parameterBag = $parameterBag;
	}

	public function processNotify(array $params): ApiResponse
	{
		$type = $params['type'];
		switch ($type) {
			case NotificationService::NOTIFICATION_TYPE_TEAM:
				$data = [
					'title' => $params['title'],
					'message' => $params['message'],
					'status' => TeamNotification::STATUS_SUCCESS,
					'date' => (new \DateTime())->format('Y-m-d'),
				];
				$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_TEAM, null, $data);
				break;
			case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
				$entityName = "App\\Model\\Entity\\{$params['entity_name']}";
				$funcName = $params['function_name'];
				try {
					$entity = $this->em->getRepository($entityName)->find($params['entity_id']);
					if (!$entity) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'entity: '.$entityName);
					}

					if (!method_exists($entityName, $funcName)) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'function: '.$funcName);
					}
					$emailAddress = $entity->$funcName();
					$templateId = $this->postmarkSrv->getTemplateId($params['template']);
					$data = $params['variables'];
					$data['template'] = $templateId;
					$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_PM_EMAIL, $emailAddress, $data);
				} catch (MappingException) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'entity_name');
				} catch (\InvalidArgumentException $ex) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'template');
				} catch (\Throwable) {
					return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
				}
				break;
			case NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL:
				$entityName = "App\\Model\\Entity\\{$params['entity_name']}";
				$funcName = $params['function_name'];
				try {
					$entity = $this->em->getRepository($entityName)->find($params['entity_id']);
					if (!$entity) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'entity: '.$entityName);
					}

					if (!method_exists($entityName, $funcName)) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'function: '.$funcName);
					}
					$emailAddress = $entity->$funcName();

					$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $emailAddress, [
						'subject' => $params['subject'],
						'from' => $params['from'] ?? null,
						'fromName' => $params['from_name'] ?? null,
						'template' => $params['template'] ?? null,
						'data' => $params['variables'],
						'attachments' => $params['attachments'] ?? null,
					]);
				} catch (MappingException) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'entity_name');
				} catch (\InvalidArgumentException $ex) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'template');
				} catch (\Throwable $t) {
					$throw = $t;

					return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
				}
				break;
			case NotificationService::NOTIFICATION_TYPE_SMS:
				$entityName = "App\\Model\\Entity\\{$params['entity_name']}";
				$funcName = $params['function_name'];
				try {
					$entity = $this->em->getRepository($entityName)->find($params['entity_id']);
					if (!$entity) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'entity: '.$entityName);
					}

					if (!method_exists($entityName, $funcName)) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'function: '.$funcName);
					}
					$mobile = $entity->$funcName();

					$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_SMS, $mobile, [
						'smsText' => $params['message'] ?? null,
					], 'Twilio SMS');
				} catch (MappingException) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'entity_name');
				} catch (\InvalidArgumentException $ex) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'template');
				} catch (\Throwable $t) {
					$throw = $t;

					return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
				}
				break;
			default:
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'type');
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processUploadFile(array $dataRequest): ApiResponse
	{
		/** @var UploadedFile $file */
		$file = $dataRequest['file'];

		$folderName = 'utils_files';

		$this->fileSystemSrv->createTempDir($folderName);
		$folderName = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$folderName;
		$fileName = uniqid('utils_file_').'.'.$file->guessExtension();
		$toPath = $folderName.DIRECTORY_SEPARATOR.$fileName;
		copy($file->getRealPath(), $toPath);

		$result = ['path' => $toPath];

		return new ApiResponse(data: $result);
	}

	public function processUploadExtraFiles(array $dataRequest): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser($this->requestStack->getCurrentRequest());

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		if (!isset($dataRequest['target'])) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_MISSING_PARAM,
				ApiError::$descriptions[ApiError::CODE_MISSING_PARAM],
				'target'
			);
		}
		if (!isset($dataRequest['contact_person_id'])) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_MISSING_PARAM,
				ApiError::$descriptions[ApiError::CODE_MISSING_PARAM],
				'contact_person_id'
			);
		}

		if ('project' !== $dataRequest['target'] && 'quote' !== $dataRequest['target']) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_INVALID_VALUE,
				ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
				'target'
			);
		}

		$contactPerson = $this->contactPersonRepo->find($dataRequest['contact_person_id']);
		if (!$contactPerson) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$customer = $contactPerson->getCustomersPerson()?->getCustomer();
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}
		$targetKey = CustomerportalFilesProjectsProcessCommand::TYPE_CP_PROJECT_EXTRA_FILES;
		if ('quote' === $dataRequest['target']) {
			$targetKey = CustomerportalFilesProjectsProcessCommand::TYPE_CP_QUOTES_EXTRA_FILES;
		}

		/** @var UploadedFile $file */
		$file = $dataRequest['file'];
		$uploadId = uniqid('file_');
		$today = (new \DateTime('now'))->format('Y-m-d');
		$toPath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.
			$customer->getId().DIRECTORY_SEPARATOR.
			$contactPerson->getId().DIRECTORY_SEPARATOR.
			$today.DIRECTORY_SEPARATOR.
			$file->getClientOriginalName();
		$this->fileSystemSrv->createDirectory(
			$this->fileSystemSrv->filesPath,
			$customer->getId().DIRECTORY_SEPARATOR.
			$contactPerson->getId().DIRECTORY_SEPARATOR.$today
		);
		if (!copy($file->getRealPath(), $toPath)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_UNABLE_UPLOAD_FILE,
				ApiError::$descriptions[ApiError::CODE_UNABLE_UPLOAD_FILE]
			);
		}
		$data = (object) [
			'Key' => $uploadId,
			'owner' => $user->getId(),
			'EntityName' => $targetKey,
			'FilePath' => $toPath,
			'FileName' => $file->getClientOriginalName(),
			'FileSize' => $file->getSize(),
			'Token' => null,
			'CreatedAt' => (new \DateTime('now'))->getTimestamp(),
		];
		$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_PENDING_FILES, [$uploadId => serialize($data)]);
		$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, microtime(true), $uploadId);

		$fileContent = file_get_contents($file->getPathname());
		unlink($file->getRealPath());

		return new ApiResponse(
			data: [
				'uploadId' => $uploadId,
				'fileStats' => $this->tikaConnector->getFileStats($file->getClientOriginalName(), $fileContent),
			]
		);
	}

	public function processGetImageToken(): ApiResponse
	{
		$result = [];
		try {
			$imageTokenResult = $this->cloudflaredConnector->getDirectUpload();
			if (!$imageTokenResult) {
				return new ErrorResponse(Response::HTTP_SERVICE_UNAVAILABLE, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
			}

			$result = $imageTokenResult->getRaw()['result'];
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting image token.', $thr);
		}

		return new ApiResponse(data: $result);
	}

	public function processMacro(array $dataRequest): ApiResponse
	{
		return $this->baseUtilsHandler->processMacro($dataRequest);
	}

	public function processEnclosures(array $dataRequest): ApiResponse
	{
		$path = $dataRequest['path'];
		$outputZipFile = "$path/output/output.zip";
		$outputResultFile = "$path/output/output.json";
		$externalToolsPath = $this->parameterBag->get('app.files.tools.path');
		$fullPath = "$externalToolsPath/enclosures/";
		$command = "$fullPath/enclosure_memory_exchange";
		$process = new Process([
			$command,
			$path,
		], null, null, null, 3600);
		$process->setWorkingDirectory($fullPath);
		try {
			$process->mustRun();
		} catch (ProcessFailedException $ex) {
			return new ErrorResponse(
				Response::HTTP_BAD_GATEWAY,
				ApiError::CODE_MACRO_RUN_ERROR,
				$ex->getMessage()
			);
		}

		$this->fileSystemSrv->changeStorage($this->parameterBag->get('app.files.temp.bucket'));
		if (!$this->fileSystemSrv->exists($outputZipFile)) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
		}
		if (!$this->fileSystemSrv->exists($outputResultFile)) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
		}
		$resultFile = $this->fileSystemSrv->download($outputResultFile);

		$result = json_decode($resultFile);

		$temporalUrl = $this->fileSystemSrv->getTemporaryUrl($outputZipFile);

		return new ApiResponse(['url' => $temporalUrl, 'result' => $result]);
	}
}
