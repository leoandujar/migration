<?php

namespace App\Command\Command;

use Symfony\Component\Console\Command\Command;
use App\Service\Notification\NotificationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationProcessCommand extends Command
{
	private NotificationService $notificationService;

	public function __construct(NotificationService $notificationSrv, $name = null)
	{
		parent::__construct($name);
		$this->notificationService = $notificationSrv;
	}

	protected function configure(): void
	{
		$this
			->setName('notifications:process')
			->setDescription('Notification: Read all the notifications from queue and send the notifications.');
	}

	/**
	 * @throws \Throwable
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('Starting notification sender');

		return $this->notificationService->sendNotification();
	}
}
