<?php

namespace App\Workflow;

use App\Model\Entity\WFHistory;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\WorkflowRepository;
use App\Service\LoggerService;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow as RegistryWorkflow;

class BasicWorkflow implements WorkflowInterface
{
	protected Registry $registry;
	protected LoggerService $loggerSrv;
	protected EntityManagerInterface $em;
	protected WorkflowRepository $workflowRepository;
	protected ?RegistryWorkflow $registryWorkflow = null;
	protected ?WFHistory $history = null;

	public function __construct(
		EntityManagerInterface $em,
		Registry $registry,
		LoggerService $loggerSrv,
		WorkflowRepository $workflowRepository
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->workflowRepository = $workflowRepository;
	}

	public function run(string $name, WFParams $params = null): void
	{
		$this->loggerSrv->addInfo("Starting $name.");

		/** @var WFWorkflow $workflow */
		$workflow = $this->workflowRepository->findOneBy(['name' => $name]);

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

		$history = new WFHistory();
		$history->setCreatedAt(new \DateTime());
		$history->setInfo(sprintf('Date: %s', $history->getCreatedAt()->format('Y-m-d H:i:s')));
		$history->setWorkflowId($workflow->getId());
		$history->setName($workflow->getName());
		$history->setRemoved(false);
		$this->history = $history;
		$this->registryWorkflow = $this->registry->get($history, 'attestation');
	}
}
