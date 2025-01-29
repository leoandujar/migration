<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use App\Workflow\Services;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\WFWorkflow;
use App\Model\Entity\AVWorkflowMonitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Workflow\WorkflowServiceFactory;

class WorkflowRunCommand extends Command
{
	public bool $hidden = true;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private WorkflowServiceFactory $workflowSrvFactory;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		WorkflowServiceFactory $workflowSrvFactory,
		?string $name = null,
	) {
		parent::__construct($name);
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->workflowSrvFactory = $workflowSrvFactory;
	}

	protected function configure(): void
	{
		$this
			->setName('workflow:run')
			->setDescription('Run workflow.')
			->addArgument(
				'service_name',
				InputArgument::REQUIRED,
				'The service name of the workflow (ex workflow.test)'
			)
			->addArgument(
				'monitor_id',
				InputArgument::OPTIONAL,
				'The workflow monitor id related to this instance.'
			);
	}

	/**
	 * @throws \Exception
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$name = $input->getArgument('service_name');
		$monitorId = $input->getArgument('monitor_id');
		$output->writeln(sprintf('Running the service %s', $name));
		/**
		 * @var WFWorkflow $wf
		 */
		$wf =  $this->em->getRepository(WFWorkflow::class)->findOneBy(['name' => $name]);
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
				$output->writeln(sprintf('Workflow type %s is not defined', $wf->getType()));

				return Command::FAILURE;
		}
		try {
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
			$output->writeln('Service finished');
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError("Error running workflow $name", $thr);

			return Command::FAILURE;
		}
		$wf->setLastRunAt(new \DateTime('now'));
		$this->em->persist($wf);
		$this->em->flush();

		return Command::SUCCESS;
	}
}
