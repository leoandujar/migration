<?php

namespace App\MessageHandler;

use App\Command\Services\CommandProcessQueueService;
use App\Message\CommandsProcessMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CommandsProcessMessageHandler
{
	private CommandProcessQueueService $commandQueueSrv;

	public function __construct(
		CommandProcessQueueService $commandQueueSrv,
	) {
		$this->commandQueueSrv = $commandQueueSrv;
	}

	public function __invoke(CommandsProcessMessage $message): void
	{
		$isLocked = $message->getIsLocked();
		$output = $message->getOutput();

		if (!$isLocked) {
			$output->writeln('The command is already running in another process.');

			return;
		}

		$this->commandQueueSrv->dequeueAndProcess($output);
	}
}
