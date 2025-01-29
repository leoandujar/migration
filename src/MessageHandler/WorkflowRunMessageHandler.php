<?php

namespace App\MessageHandler;

use App\Message\WorkflowRunMessage;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
use App\Service\LoggerService;
use App\Workflow\WorkflowServiceFactory;
use Doctrine\ORM\EntityManagerInterface;
use App\Workflow\Services;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WorkflowRunMessageHandler
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
	public function __invoke(WorkflowRunMessage $message): void
    {
		try {
			$name = $message->getName();
			$monitorId = $message->getMonitorId();

			$wf = $this->em->getRepository(WFWorkflow::class)->findOneBy(['name' => $name]);
			if (null === $wf) {
				throw new \Exception(sprintf('Not workflow %s defined', $name));
			}

			switch ($wf->getType()) {
				case WFWorkflow::TYPE_XTRF_PROJECT:
					$startClassName = Services\XtrfProject\Start::class;
					break;
				case WFWorkflow::TYPE_XTRF_PROJECT_V2:
					$startClassName = Services\XtrfProjectV2\Start::class;
					break;
				case WFWorkflow::TYPE_CREATE_ZIP:
					$startClassName = Services\CreateZip\Start::class;
					break;
				case WFWorkflow::TYPE_XTM_PROJECT:
					$startClassName = Services\XtmProject\Start::class;
					break;
				case WFWorkflow::TYPE_XTM_GITHUB:
					$startClassName = Services\XtmGithub\Start::class;
					break;
				case WFWorkflow::TYPE_EMAIL_PARSING:
					$startClassName = Services\EmailParsing\Start::class;
					break;
				case WFWorkflow::TYPE_XTM_TM:
					$startClassName = Services\XtmTm\Start::class;
					break;
				case WFWorkflow::TYPE_ATTESTATION:
					$startClassName = Services\Attestation\Start::class;
					break;
				case WFWorkflow::TYPE_XTRF_QBO:
					$startClassName = Services\XtrfQbo\Start::class;
					break;
				case WFWorkflow::TYPE_BL_XTRF:
					$startClassName = Services\BlXtrf\Start::class;
					break;
				default:
					throw new \Exception(sprintf('Workflow type %s is not defined', $wf->getType()));
			}

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
