<?php

namespace App\MessageHandler;

use App\Command\Services\StripeQueueService;
use App\Linker\Services\RedisClients;
use App\Message\ConnectorStripeProcessMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConnectorStripeProcessMessageHandler
{
	public const LIMIT_TO_PROCESS = 50;

	private StripeQueueService $stripeQueueService;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;

	public function __construct(
		StripeQueueService $stripeQueueSrv,
		RedisClients $redisClients,
		LoggerService $loggerSrv,
	) {
		$this->stripeQueueService = $stripeQueueSrv;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->loggerSrv->setSubcontext(self::class);
	}

	/**
	 * @throws \RedisException
	 */
	public function __invoke(ConnectorStripeProcessMessage $message): void
	{
		$totalToProcess = $message->getLimit();
		while ($totalToProcess-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_STRIPE_PAYMENTS)) !== null) {
			if (null === $payload) {
				$msg = 'Stripe Payment queue is empty.';
				$this->loggerSrv->addWarning($msg);
				$totalToProcess = 0;
			}
			try {
				if (($stripeObj = unserialize($payload)) === false || !is_object($stripeObj)) {
					throw new \Exception("Unable to unserialize payload with data $payload");
				}

				switch ($stripeObj->type) {
					case 'payment_intent.succeeded':
						$processedStripeObj = $this->stripeQueueService->processPayload($stripeObj);

						break;
					case 'checkout.session.completed':
						$processedStripeObj = $this->stripeQueueService->processCheckoutPayload($stripeObj);

						break;
					default:
						$processedStripeObj = false;
						$this->loggerSrv->addInfo('Processing Stripe Payment with Payload failed');
						break;
				}

				if (true !== $processedStripeObj) {
					$obj = is_object($processedStripeObj) ? $processedStripeObj : $stripeObj;
					$this->enqueueWebhook(payload: $obj);
				}
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error processing Stripe Payments. Check logs for more details.', $thr);
				$this->enqueueWebhook(payload: $payload);
				continue;
			}
		}
	}

	/**
	 * @throws \RedisException
	 */
	private function enqueueWebhook($payload): void
	{
		if ($payload->countFailed > RedisClients::DEFAULT_QUEUE_COUNT_FAILURE) {
			$msg = "Payment Queue {$payload->data->id} exceeded the maximum of allowed  attempts. It will not be added to the queue";
			$this->loggerSrv->addError($msg, [$payload]);
		} else {
			++$payload->countFailed;
			$msg = "Adding again to queue the Stripe Payment {$payload->data->id} with count $payload->countFailed";
			$this->loggerSrv->addInfo($msg);
			$position = $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_STRIPE_PAYMENTS, serialize($payload));
			if ($position < 0) {
				$this->loggerSrv->addError("Unable to Enqueue the payload for Stripe Payment => $payload");
				$this->loggerSrv->addInfo("Unable to enqueue again the Stripe Payment {$payload->data->id}");

				return;
			}
		}
	}
}
