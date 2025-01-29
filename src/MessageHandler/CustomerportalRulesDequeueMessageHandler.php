<?php

namespace App\MessageHandler;

use App\Linker\Services\RedisClients;
use App\Message\CustomerportalRulesDequeueMessage;
use App\Service\LoggerService;
use App\Service\XtrfWebhookService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CustomerportalRulesDequeueMessageHandler
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private XtrfWebhookService $xtrfWebhookSrv;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		XtrfWebhookService $xtrfWebhookSrv,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->xtrfWebhookSrv = $xtrfWebhookSrv;
	}

	public function __invoke(CustomerportalRulesDequeueMessage $message): void
	{
		do {
			$dequeueLimit = null != $message->getDequeueLimit() ? $message->getDequeueLimit() : self::LIMIT;
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_RULES_COMMAND_QUEUE)) !== null) {
				try {
					if (null === $payload) {
						$msg = 'Rules queue is empty.';
						$this->loggerSrv->addWarning($msg);
						break;
					}

					if (($payload = unserialize($payload)) === false) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}
					$this->xtrfWebhookSrv->processEvents(event: $payload['event'], objectData: $payload['object']);
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Rules command queue. Check logs for more details.', $thr);
					if (null !== $payload) {
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_RULES_COMMAND_QUEUE, (array) serialize($payload));
					}
					continue;
				}
			}
		} while (0);
	}
}
