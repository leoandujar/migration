<?php

namespace App\MessageHandler;

use App\Message\WorkflowCleanHistoryMessage;
use App\Model\Entity\WFHistory;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WorkflowCleanHistoryMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	/**
	 * WorkflowCleanHistoryCommand constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
	}

	public function __invoke(WorkflowCleanHistoryMessage $message): void
	{
		$this->loggerSrv->addInfo('Clean the history from database.');
		$days = $message->getDays();
		if (!is_numeric($days)) {
			$this->loggerSrv->addError(sprintf('Days should be an integer: %s given', $days));
		}
		$days = intval($days);
		$toRemove = $this->em->getRepository(WFHistory::class)->getHistories(days: $days);
		$count = 0;
		foreach ($toRemove as $history) {
			$this->em->remove($history);

			++$count;
		}
		$this->em->flush();

		$this->loggerSrv->addInfo(sprintf('Clean expired finished with %d files deleted', $count));
	}
}
