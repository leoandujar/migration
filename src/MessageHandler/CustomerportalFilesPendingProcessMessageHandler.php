<?php

namespace App\MessageHandler;

use App\Connector\Xtrf\XtrfConnector;
use App\Linker\Services\RedisClients;
use App\Message\CustomerportalFilesPendingProcessMessage;
use App\Service\LoggerService;
use App\Service\MercureService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CustomerportalFilesPendingProcessMessageHandler
{
	private XtrfConnector $xtrfCon;
	private LoggerService $loggerSrv;
	private MercureService $mercureSrv;
	private RedisClients $redisClients;
	public const TYPE_CP_QUOTES_EXTRA_FILES = 'quotes';
	public const TYPE_CP_PROJECT_EXTRA_FILES = 'projects';

	public function __construct(
		XtrfConnector $xtrfCon,
		RedisClients $redisClients,
		MercureService $mercureSrv,
		LoggerService $loggerSrv,
	) {
		$this->xtrfCon = $xtrfCon;
		$this->loggerSrv = $loggerSrv;
		$this->mercureSrv = $mercureSrv;
		$this->redisClients = $redisClients;
	}

	/**
	 * @throws \Throwable
	 * @throws \RedisException
	 */
	public function __invoke(CustomerportalFilesPendingProcessMessage $message): void
	{
		$data = $message->getData();
		if (!$data) {
			$msg = 'File Process Instance was called with empty data. Aborting.';
			$this->loggerSrv->addError($msg);
			throw new \Exception($msg);
		}

		$data = json_decode($data);
		if (null === $data) {
			$msg = 'Data could not be decoded. Aborting.';
			$this->loggerSrv->addError($msg);
			throw new \Exception($msg);
		}

		try {
			switch ($data->EntityName) {
				case self::TYPE_CP_PROJECT_EXTRA_FILES:
				case self::TYPE_CP_QUOTES_EXTRA_FILES:
					$this->loggerSrv->addInfo("Processing Customer File {$data->EntityName} {$data->FileSize} {$data->Key} {$data->FilePath}", (array) $data);
					$fileContent = file_get_contents($data->FilePath);
					$processParams[] = [
						'name' => 'file',
						'filename' => $data->FileName,
						'contents' => $fileContent,
					];
					$uploadResponse = $this->xtrfCon->uploadProjectFile($processParams);
					if (!$uploadResponse->isSuccessfull()) {
						if (property_exists($data, 'owner')) {
							$this->mercureSrv->publish([
								'fileId' => $data->Key,
								'status' => MercureService::STATUS_FAILED,
								'entityType' => $data->EntityName,
							], $data->owner);
						}
						$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_AWAITING_FILES, $data->Key);
						$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, [$data->Key => microtime(true)]);
						break;
					}
					$data->Token = $uploadResponse->getToken();
					$this->loggerSrv->addInfo("Processed Successfully Customer File $data->EntityName $data->Key $data->FilePath", (array) $data);
					$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_AWAITING_FILES, [$data->Key => serialize($data)]);
					$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_PENDING_FILES, $data->Key);
					break;
				default:
					$this->loggerSrv->addError("Unrecognized file entity name $data->entityName");
			}
		} catch (\Throwable $thr) {
			if ($data && $data->Key) {
				$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, [$data->Key => microtime(true)]);
			}
			$this->loggerSrv->addError('Error processing file', $thr);

			throw $thr;
		}
	}
}
