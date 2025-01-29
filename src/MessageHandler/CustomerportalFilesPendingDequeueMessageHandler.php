<?php

namespace App\MessageHandler;

use App\Linker\Services\RedisClients;
use App\Message\CustomerportalFilesPendingDequeueMessage;
use App\Message\CustomerportalFilesPendingProcessMessage;
use App\Service\LoggerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class CustomerportalFilesPendingDequeueMessageHandler
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private ParameterBagInterface $parameterBag;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		ParameterBagInterface $parameterBag,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->parameterBag = $parameterBag;
		$this->bus = $bus;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function __invoke(CustomerportalFilesPendingDequeueMessage $message): void
	{
		do {
			$dequeueLimit = self::LIMIT;
			while ($dequeueLimit-- > 0 && ($elementId = $this->redisClients->redisMainDB->zrange(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, 0, 0)) !== null) {
				try {
					$payload = $fileObj = null;
					if (!empty($elementId)) {
						$elementId = array_shift($elementId);
						$this->redisClients->redisMainDB->zrem(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, $elementId);
						$payload = $this->redisClients->redisMainDB->hmget(RedisClients::SESSION_KEY_PENDING_FILES, $elementId);
					}

					if (!$payload) {
						$values = $this->redisClients->redisMainDB->hgetall(RedisClients::SESSION_KEY_PENDING_FILES);
						if (!$values) {
							break;
						}
						if (!count($values)) {
							$this->loggerSrv->addInfo('Files queue is empty.');
							continue;
						}
						$payload = $values;
					}

					$payload = array_shift($payload);
					if (null === $payload) {
						$this->loggerSrv->addInfo('Files queue is empty.');
						continue;
					}
					if (($fileObj = unserialize($payload)) === false) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}

					$this->loggerSrv->addInfo('File found...processing.');
					$data = json_encode($fileObj);

					try {
						$this->bus->dispatch(new CustomerportalFilesPendingProcessMessage($data));
					} catch (\Throwable $thr) {
						if (null !== $fileObj) {
							$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, [$fileObj->Key => microtime(true)]);
						}
						$this->loggerSrv->addError('Error dispatching message. File added back to queue.', $thr);
					}

				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing file data. Check logs for more details.', $thr);
					continue;
				}
			}
		} while (0);
	}
}
