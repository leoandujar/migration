<?php

namespace App\MessageHandler;

use App\Linker\Services\RedisClients;
use App\Message\CustomerportalFilesProjectsDequeueMessage;
use App\Message\CustomerportalFilesProjectsProcessMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class CustomerportalFilesProjectsDequeueMessageHandler
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->bus = $bus;
	}

	public function __invoke(CustomerportalFilesProjectsDequeueMessage $message): void
	{
		do {
			$this->loggerSrv->addInfo('PROCESSING PROJECTS-QUOTES QUEUE.');
			$dequeueLimit = null != $message->getLimit() ? $message->getLimit() : 10;
			$queueName = $message->getQueueName();
			if (!$queueName) {
				$this->loggerSrv->addError('Queue name is empty. Aborting.');
				break;
			}
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop($queueName)) !== null) {
				try {
					$this->loggerSrv->addInfo('Entity found...processing.');
					$idRedis = uniqid('files_data_');
					$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, [$idRedis => $payload]);
					try {
						$this->bus->dispatch(new CustomerportalFilesProjectsProcessMessage(data: $idRedis, queue: $queueName));
						$this->loggerSrv->addInfo('PROCESS END.');
					} catch (\Exception $thr) {
						$this->loggerSrv->addError('Exception while processing project files.', $thr);
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_PROJECTS_QUOTES_ERROR, $payload);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Project-Quote entity data. Check logs for more details.', $thr);
					if (null !== $payload) {
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_PROJECTS_QUOTES, $payload);
					}
					continue;
				}
			}
		} while (0);
	}
}
