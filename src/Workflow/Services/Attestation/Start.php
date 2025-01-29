<?php

namespace App\Workflow\Services\Attestation;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFHistory;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\ContactPersonRepository;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\WorkflowRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Start implements WorkflowInterface
{
	private ContactPersonRepository $contactPersonRepo;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private WorkflowRepository $workflowRepository;
	private LoggerService $loggerSrv;
	private Registry $registry;

	public function __construct(
		EntityManagerInterface $em,
		Registry $registry,
		LoggerService $loggerSrv,
		ContactPersonRepository $contactPersonRepo,
		WorkflowRepository $workflowRepository,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->workflowRepository = $workflowRepository;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ATTESTATION);
		$this->contactPersonRepo = $contactPersonRepo;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->registry = $registry;
	}

	public function run($name, ?WFParams $parameter = null): void
	{
		$this->loggerSrv->addInfo('Starting Attestation workflow.');

		/** @var WFWorkflow $workflow */
		$workflow = $this->workflowRepository->findOneBy(['name' => $name]);

		$this->loggerSrv->addInfo('Checking if data param is present');
		if (null === $workflow) {
			$msg = "Workflow $name not found";
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		$parameter = $parameter ?? $workflow->getParameters();
		if (null === $parameter) {
			$msg = "Workflow $name has not params.";
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		$params = $parameter->getParams();
		$history = WFHistory::instance($workflow);

		try {
			$registryWorkflow = $this->registry->get($history, 'attestation');

			if (empty($params['monitor_id'])) {
				$msg = "Workflow $name has not monitor ID associated. Unable to continue";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			$monitorID = $params['monitor_id'];
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($monitorID);
			if (null === $monitorObj) {
				$msg = "Workflow $name could not found the monitor with id $monitorID on DB. Unable to continue";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}
			if (null === $monitorObj) {
				$msg = "Workflow $name could not found the monitor with id $monitorID on DB. Unable to continue";
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			if (null !== $monitorObj->getDetails() && !empty($monitorObj->getDetails()['params'])) {
				$params = $monitorObj->getDetails()['params'];
				$params['monitor_id'] = $monitorID;
			}
			$template = $params['template'];
			if (isset($template['contactId'])) {
				$contactPerson = $this->contactPersonRepo->find($template['contactId']);

				if (!$contactPerson) {
					$msg = "Contact Person with id {$template['$contactId']} not found. Unable to continue.";
					$this->loggerSrv->addError($msg);
					throw new BadRequestHttpException($msg);
				}

				$template['name'] = $contactPerson->getName();
				$template['address'] = $contactPerson->getAddressAddress();
				$template['email'] = $contactPerson->getEmail();
			}

			$history->setContext([
				'filters' => $params['filters'] ?? [],
				'mapping' => $params['mapping'] ?? [],
				'notificationTo' => $params['to'] ?? '',
				'template' => $template ?? [],
				'notification_type' => $parameter->getNotificationType(),
				'notification_target' => $parameter->getNotificationTarget(),
				'params' => $params,
				'monitor_id' => $monitorID,
			]);

			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
			$registryWorkflow->apply($history, 'start');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting Attestation workflow', $thr);
			throw $thr;
		}
	}
}
