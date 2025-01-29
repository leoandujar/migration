<?php

namespace App\MessageHandler;

use App\Linker\Services\RedisClients;
use App\Message\WorkflowScheduleQueueMessage;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
use App\Model\Entity\Workflow;
use App\Model\Repository\WorkflowRepository;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Cron\CronExpression;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WorkflowScheduleQueueMessageHandler
{
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private EntityManagerInterface $em;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		EntityManagerInterface $em,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
	}

	public function __invoke(WorkflowScheduleQueueMessage $message): void
	{
		$workflowList = $this->em->getRepository(Workflow::class)->findBy(['runAutomatically' => true]);
		$this->loggerSrv->addInfo('FOUND AUTO WORKFLOW COUNT '.count($workflowList));
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
