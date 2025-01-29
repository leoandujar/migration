<?php

namespace App\MessageHandler;

use App\Message\WorkflowDispatchMessage;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
use App\Service\LoggerService;
use App\Workflow\Services;
use App\Workflow\WorkflowServiceFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WorkflowDispatchMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private WorkflowServiceFactory $workflowSrvFactory;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		WorkflowServiceFactory $workflowSrvFactory,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->workflowSrvFactory = $workflowSrvFactory;
	}

	/**
	 * @throws \DateMalformedStringException
	 * @throws \Throwable
	 */
	public function __invoke(WorkflowDispatchMessage $message): void
	{
		try {
			$name = $message->getName();
			$monitorId = $message->getMonitorId();

			$wf = $this->em->getRepository(WFWorkflow::class)->findOneBy(['name' => $name]);
			if (null === $wf) {
				throw new \Exception(sprintf('Not workflow %s defined', $name));
			}

			$startClassName = match ($wf->getType()) {
				WFWorkflow::TYPE_XTRF_PROJECT => Services\XtrfProject\Start::class,
				WFWorkflow::TYPE_XTRF_PROJECT_V2 => Services\XtrfProjectV2\Start::class,
				WFWorkflow::TYPE_CREATE_ZIP => Services\CreateZip\Start::class,
				WFWorkflow::TYPE_XTM_PROJECT => Services\XtmProject\Start::class,
				WFWorkflow::TYPE_XTM_GITHUB => Services\XtmGithub\Start::class,
				WFWorkflow::TYPE_EMAIL_PARSING => Services\EmailParsing\Start::class,
				WFWorkflow::TYPE_XTM_TM => Services\XtmTm\Start::class,
				WFWorkflow::TYPE_ATTESTATION => Services\Attestation\Start::class,
				WFWorkflow::TYPE_XTRF_QBO => Services\XtrfQbo\Start::class,
				WFWorkflow::TYPE_BL_XTRF => Services\BlXtrf\Start::class,
				default => throw new \Exception(sprintf('Workflow type %s is not defined', $wf->getType())),
			};

			$parameters = clone $wf->getParameters();
			$params = $parameters->getParams();
			if (!empty($monitorId)) {
				$monitorObj = $this->em->getRepository(AVWorkflowMonitor::class)->find($monitorId);
				if ($monitorObj && null !== $monitorObj->getDetails() && !empty($monitorObj->getDetails()['params'])) {
					$params = $monitorObj->getDetails()['params'];
				}
				$params['monitor_id'] = $monitorId;
			}
			$parameters->setParams($params);
			$service = $this->workflowSrvFactory->getStartClass($startClassName);
			$service->Run($name, $parameters);

			$wf->setLastRunAt(new \DateTime('now'));
			$this->em->persist($wf);
			$this->em->flush();

		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error al crear el usuario: '.$thr->getMessage());
			throw $thr;
		}
	}
}
