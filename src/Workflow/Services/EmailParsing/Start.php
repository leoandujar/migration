<?php

namespace App\Workflow\Services\EmailParsing;

use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFHistory;
use App\Model\Entity\WFWorkflow;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\WorkflowRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Start implements WorkflowInterface
{
	private $loggerSrv;
	private $registry;
	private $workflowRepository;
	private EntityManagerInterface $em;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		WorkflowRepository $workflowRepository,
		EntityManagerInterface $em
	) {
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->workflowRepository = $workflowRepository;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_EMAIL_PARSING);
	}

	public function run($name, WFParams $parameter = null): void
	{
		$this->loggerSrv->addInfo('Starting EmailParsing workflow.');

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
		$history = new WFHistory();
		$history->setCreatedAt(new \DateTime());
		$history->setInfo(sprintf('Date: %s', $history->getCreatedAt()->format('Y-m-d H:i:s')));
		$history->setWorkflowId($workflow->getId());
		$history->setName($workflow->getName());
		$history->setRemoved(false);

		try {
			$registryWorkflow = $this->registry->get($history, 'email_parsing');
			$data = $params['data'] ?? [];
			$mapping = $params['mapping'] ?? [];
			unset($params['data'], $params['mapping']);
			$history->setContext([
				'data' => $data,
				'mapping' => $mapping,
				'notification_type' => $parameter->getNotificationType(),
				'notification_target' => $parameter->getNotificationTarget(),
				'params' => $params,
			]);

			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($history);
			$this->em->flush();
			if ($registryWorkflow->can($history, 'initialized')) {
				$registryWorkflow->apply($history, 'initialized');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting EmailParsing workflow', $thr);
			throw $thr;
		}
	}
}
