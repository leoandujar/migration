<?php

namespace App\Workflow\Services\XtmTm;

use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFHistory;
use App\Model\Entity\WFWorkflow;
use App\Apis\Shared\Util\UtilsService;
use App\Workflow\Services\WorkflowInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Repository\WorkflowRepository;

class Start implements WorkflowInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private WorkflowRepository $workflowRepository;
	private EntityManagerInterface $em;

	/**
	 * Start constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		WorkflowRepository $workflowRepository,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->workflowRepository = $workflowRepository;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public function run($name, WFParams $parameter = null): void
	{
		$this->loggerSrv->addInfo('Starting xtm file generator workflow.');
		/**
		 * @var $workflow WFWorkflow
		 */
		$workflow = $this->workflowRepository->findOneBy(['name' => $name]);
		if (null === $workflow) {
			$this->loggerSrv->addWarning(sprintf('no workflow named %s was found', $name));

			return;
		}
		$parameter = $parameter ?? $workflow->getParameters();
		$request = WFHistory::instance($workflow);
		try {
			$registryWorkflow = $this->registry->get($request, 'xtm_tm');
			$params = $parameter->getParams();
			if (!$this->valid($params)) {
				return;
			}
			list($start, $end) = UtilsService::getRange($params['range_start'], $params['range']);
			$request->setContext(
				array_merge(
					$request->getContext(),
					[
					'start' => $start->format('Y-m-d'),
					'end' => $end->format('Y-m-d'),
					'source_disk' => $params['source_disk'],
					'notify' => [
						'message' => '',
						'status' => 'error',
						'date' => (new \DateTime())->format('Y-m-d'),
						'languages' => [],
						'title' => $workflow->getName(),
					],
				]
				)
			);
			if (!$this->em->isOpen()) {
				$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
			}
			$this->em->persist($request);
			$this->em->flush();
			$registryWorkflow->apply($request, 'start');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error while starting Xtm file generator workflow.', $thr);
		}
	}

	private function valid(array $params): bool
	{
		if (!array_key_exists('range_start', $params)) {
			$this->loggerSrv->addError('range_start parameter not defined');

			return false;
		}
		if (!array_key_exists('customer', $params)) {
			$this->loggerSrv->addError('customer parameter not defined');

			return false;
		}
		if (!array_key_exists('xtm_customer', $params)) {
			$this->loggerSrv->addError('xtm_customer parameter not defined');

			return false;
		}
		if (!array_key_exists('range', $params)) {
			$this->loggerSrv->addError('range parameter not defined');

			return false;
		}
		if (!array_key_exists('range_field', $params)) {
			$this->loggerSrv->addError('range_field parameter not defined');

			return false;
		}
		if (!array_key_exists('file_type', $params)) {
			$this->loggerSrv->addError('file_type parameter not defined');

			return false;
		}
		if (!array_key_exists('customer_webhook', $params)) {
			$this->loggerSrv->addError('customer_webhook parameter not defined');

			return false;
		}

		return true;
	}
}
