<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use App\Command\Services\StripeQueueService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectorsStripeProcessCommand extends Command
{
	use LockableTrait;

	private const LIMIT_TO_PROCESS = 50;

	private StripeQueueService $stripeQueueService;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;

	public function __construct(
		StripeQueueService $stripeQueueSrv,
		RedisClients $redisClients,
		LoggerService $loggerSrv
	) {
		parent::__construct();
		$this->stripeQueueService = $stripeQueueSrv;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->loggerSrv->setSubcontext(self::class);
	}

	protected function configure(): void
	{
		$this
			->setName('connectors:stripe:process')
			->setDescription('Stripe: Process the queue with Stripe object that record the success invoices payments.')
			->addOption(
				'limit',
				'l',
				InputOption::VALUE_REQUIRED,
				'Maximum number to fetch from the queue.',
				self::LIMIT_TO_PROCESS
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('Entering to process Stripe Queue.');
		$totalToProcess = intval($input->getOption('limit'));

		while ($totalToProcess-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_STRIPE_PAYMENTS)) !== null) {
			if (null === $payload) {
				$msg = 'Stripe Payment queue is empty.';
				$this->loggerSrv->addWarning($msg);
				$output->writeln($msg);
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
					$this->enqueueWebhook($obj, $output);
				}
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error processing Stripe Payments. Check logs for more details.', $thr);
				$this->enqueueWebhook($payload, $output);
				continue;
			}
		}

		return Command::SUCCESS;
	}

	private function enqueueWebhook($payload, OutputInterface $output): bool
	{
		if ($payload->countFailed > RedisClients::DEFAULT_QUEUE_COUNT_FAILURE) {
			$msg = "Payment Queue {$payload->data->id} exceeded the maximum of allowed  attempts. It will not be added to the queue";
			$this->loggerSrv->addError($msg, [$payload]);
			$output->writeln($msg);
		} else {
			++$payload->countFailed;
			$msg = "Adding again to queue the Stripe Payment {$payload->data->id} with count $payload->countFailed";
			$this->loggerSrv->addInfo($msg);
			$output->writeln($msg);
			$position = $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_STRIPE_PAYMENTS, serialize($payload));
			if ($position < 0) {
				$this->loggerSrv->addError("Unable to Enqueue the payload for Stripe Payment => $payload");
				$output->writeln("Unable to enqueue again the Stripe Payment {$payload->data->id}");

				return false;
			}
		}

		return true;
	}
}
