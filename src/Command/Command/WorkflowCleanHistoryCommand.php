<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\WFHistoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WorkflowCleanHistoryCommand extends Command
{
	private WFHistoryRepository $historyRepository;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	/**
	 * WorkflowCleanHistoryCommand constructor.
	 */
	public function __construct(
		WFHistoryRepository $historyRepository,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		string $name = null
	) {
		parent::__construct($name);
		$this->em = $em;
		$this->historyRepository = $historyRepository;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('workflow:clean:history')
			->setDescription('Workflow: Remove the history from before than the given days..')
			->addArgument(
				'days',
				InputArgument::OPTIONAL,
				'Before how many days should be removed the history',
				30
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('Clean the history from database.');
		$days = $input->getArgument('days');
		if (!is_numeric($days)) {
			$this->loggerSrv->addError(sprintf('Days should be an integer: %s given', $days));
		}
		$days = intval($days);
		$toRemove = $this->historyRepository->getHistories($days);
		$count = 0;
		foreach ($toRemove as $history) {
			$this->em->remove($history);

			++$count;
		}
		$this->em->flush();

		$output->writeln(sprintf('Clean expired finished with %d files deleted', $count));

		return Command::SUCCESS;
	}
}
