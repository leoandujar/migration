<?php

namespace App\Command\Services;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\WorkflowRepository;
use Cron\CronExpression;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use Symfony\Component\Console\Output\OutputInterface;

class WorkflowAutoProcessQueueService
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private EntityManagerInterface $em;
	private WorkflowRepository $workflowRepo;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		EntityManagerInterface $em,
		WorkflowRepository $workflowRepo
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->workflowRepo = $workflowRepo;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function dequeueAndProcess(OutputInterface $output): void
	{
		$workflowList = $this->workflowRepo->findBy(['runAutomatically' => true]);
		$output->writeln('FOUND AUTO WORKFLOW COUNT '.count($workflowList));
		/** @var WFWorkflow $workflow */
		foreach ($workflowList as $workflow) {
			$cronScheduler = new CronExpression($workflow->getRunPattern());
			$now = (new \DateTime());
			if ($cronScheduler->isDue($now)) {
				$this->loggerSrv->addInfo('Workflow '.$workflow->getName().' is scheduled to run at '.$now->format('Y-m-d H:i:s'));
				$workflowMonitor = new AVWorkflowMonitor();
				$workflowMonitor->setWorkflow($workflow);
				$this->em->persist($workflowMonitor);
				$this->em->flush();
				$this->redisClients->redisMainDB->rpush(
					RedisClients::SESSION_KEY_AWAITING_WORKFLOWS,
					json_encode(['id' => $workflowMonitor->getId()])
				);
			}
		}
	}
}
