<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use App\Model\Entity\TimeSerieStats;
use App\Model\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\QuoteRepository;
use App\Model\Repository\ProjectRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimescaleCommand extends Command
{
	private LoggerService $loggerSrv;
	private TaskRepository $taskRepo;
	private EntityManagerInterface $em;
	private QuoteRepository $quoteRepo;
	private ProjectRepository $projectRepo;

	public function __construct(
		EntityManagerInterface $em,
		TaskRepository $taskRepo,
		QuoteRepository $quoteRepo,
		ProjectRepository $projectRepo,
		LoggerService $loggerSrv,
	) {
		parent::__construct();
		$this->em = $em;
		$this->taskRepo = $taskRepo;
		$this->loggerSrv = $loggerSrv;
		$this->quoteRepo = $quoteRepo;
		$this->projectRepo = $projectRepo;
		$this->loggerSrv->setSubcontext(self::class);
	}

	protected function configure(): void
	{
		$this->setName('timescale:process');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('Collecting all open projects.');
		$projects = $this->projectRepo->getOpenedProjectsByBranch('1');
		$output->writeln(sprintf('Found %d opened projects', count($projects)));
		$output->writeln('Iterating projects');
		$data = [];
		foreach ($projects as $key => $project) {
			try {
				$output->writeln("Iterating row $key");
				$customer = $project->getCustomer();
				$manager = $project->getProjectManager();
				$coordinator = $project->getProjectCoordinator();

				$totalWordsSqlResult = $this->taskRepo->getTotalWordsByProject($project->getId());
				$workingFilesSqlResult = $this->taskRepo->getTotalWorkingFilesByProject($project->getId());

				if ($customer) {
					$customerOpenProjs = $this->projectRepo->getOpenedProjectsByEntity($customer?->getId());
					$customerOpenTasks = $this->taskRepo->getOpenedTaskByEntity($customer?->getId());
					$customerCoordinatorTasks = $this->taskRepo->getCoordinatorTaskByEntity($customer?->getId());
					$customerRequestedQuotes = $this->quoteRepo->getRequestedByEntity($customer?->getId());
					$customerAgreedCost = $this->projectRepo->getTotalAgreedAndCostEntity($customer?->getId());
					$customerTotalAgreed = $customerAgreedCost['sumTotalAgreed'] ?? 0;
					$customerTotalCost = $customerAgreedCost['sumTotalCost'] ?? 0;
					$customerTotalWords = $totalWordsSqlResult['sum'] ?? 0;
					$customerTotalWorkingFiles = $workingFilesSqlResult['sum'] ?? 0;
					if (isset($data['customers'][$customer->getId()])) {
						$customerTotalWords += $data['customers'][$customer->getId()]['totalWords'];
						$customerTotalWorkingFiles += $data['customers'][$customer->getId()]['totalWorkingFiles'];
					}
					$data['customers'][$customer->getId()] = [
						'time' => (new \DateTime('now'))->format('Y-m-d H:i:s.u'),
						'customer' => $customer,
						'projectManager' => null,
						'coordinatorManager' => null,
						'openProjects' => count($customerOpenProjs),
						'openTasks' => count($customerOpenTasks),
						'coordinatorTasks' => count($customerCoordinatorTasks),
						'requestedQuotes' => count($customerRequestedQuotes),
						'totalAgreed' => $customerTotalAgreed,
						'totalCost' => $customerTotalCost,
						'totalWords' => $customerTotalWords,
						'totalWorkingFiles' => $customerTotalWorkingFiles,
					];
				}

				if ($manager) {
					$managerOpenProjs = $this->projectRepo->getOpenedProjectsByEntity(null, $manager?->getId());
					$managerOpenTasks = $this->taskRepo->getOpenedTaskByEntity(null, $manager?->getId());
					$managerCoordinatorTasks = $this->taskRepo->getCoordinatorTaskByEntity(null, $manager?->getId());
					$managerRequestedQuotes = $this->quoteRepo->getRequestedByEntity(null, $manager?->getId());
					$managerAgreedCost = $this->projectRepo->getTotalAgreedAndCostEntity(null, $manager?->getId());
					$managerTotalAgreed = $managerAgreedCost['sumTotalAgreed'] ?? 0;
					$managerTotalCost = $managerAgreedCost['sumTotalCost'] ?? 0;
					$managerTotalWords = $totalWordsSqlResult['sum'] ?? 0;
					$managerTotalWorkingFiles = $workingFilesSqlResult['sum'] ?? 0;
					if (isset($data['managers'][$manager->getId()])) {
						$managerTotalWords += $data['managers'][$manager->getId()]['totalWords'];
						$managerTotalWorkingFiles += $data['managers'][$manager->getId()]['totalWorkingFiles'];
					}
					$data['managers'][$manager->getId()] = [
						'time' => (new \DateTime('now'))->format('Y-m-d H:i:s.u'),
						'customer' => null,
						'projectManager' => $manager,
						'coordinatorManager' => null,
						'openProjects' => count($managerOpenProjs),
						'openTasks' => count($managerOpenTasks),
						'coordinatorTasks' => count($managerCoordinatorTasks),
						'requestedQuotes' => count($managerRequestedQuotes),
						'totalAgreed' => $managerTotalAgreed,
						'totalCost' => $managerTotalCost,
						'totalWords' => $managerTotalWords,
						'totalWorkingFiles' => $managerTotalWorkingFiles,
					];
				}

				if ($coordinator) {
					$coordinatorOpenProjs = $this->projectRepo->getOpenedProjectsByEntity(null, null, $coordinator?->getId());
					$coordinatorOpenTasks = $this->taskRepo->getOpenedTaskByEntity(null, null, $coordinator?->getId());
					$coordinatorRequestedQuotes = $this->quoteRepo->getRequestedByEntity(null, null, $coordinator?->getId());
					$coordinatorAgreedCost = $this->projectRepo->getTotalAgreedAndCostEntity(null, null, $coordinator?->getId());
					$coordinatorTotalAgreed = $coordinatorAgreedCost['sumTotalAgreed'] ?? 0;
					$coordinatorTotalCost = $coordinatorAgreedCost['sumTotalCost'] ?? 0;
					$coordinatorTotalWords = $totalWordsSqlResult['sum'] ?? 0;
					$coordinatorTotalWorkingFiles = $workingFilesSqlResult['sum'] ?? 0;
					if (isset($data['coordinators'][$coordinator->getId()])) {
						$coordinatorTotalWords += $data['coordinators'][$coordinator->getId()]['totalWords'];
						$coordinatorTotalWorkingFiles += $data['coordinators'][$coordinator->getId()]['totalWorkingFiles'];
					}
					$data['coordinators'][$coordinator->getId()] = [
						'time' => (new \DateTime('now'))->format('Y-m-d H:i:s.u'),
						'customer' => null,
						'projectManager' => null,
						'coordinatorManager' => $coordinator,
						'openProjects' => count($coordinatorOpenProjs),
						'openTasks' => count($coordinatorOpenTasks),
						'coordinatorTasks' => count($coordinatorOpenTasks),
						'requestedQuotes' => count($coordinatorRequestedQuotes),
						'totalAgreed' => $coordinatorTotalAgreed,
						'totalCost' => $coordinatorTotalCost,
						'totalWords' => $coordinatorTotalWords,
						'totalWorkingFiles' => $coordinatorTotalWorkingFiles,
					];
				}
			} catch (\Throwable $thr) {
				$msg = "Error processing the Project {$project->getId()}.";
				$this->loggerSrv->addError($msg, $thr);
				$output->writeln($msg);
				continue;
			}
		}
		$count = count($data);
		if ($count) {
			$iter = 0;
			foreach ($data as $key => $datum) {
				foreach ($datum as $row) {
					++$iter;
					try {
						$output->writeln("Inserting data row $iter");

						$obj = new TimeSerieStats();
						if ('customers' === $key) {
							$obj->setCustomer($row['customer']);
						}
						if ('managers' === $key) {
							$obj->setProjectManager($row['projectManager']);
						}
						if ('coordinators' === $key) {
							$obj->setCoordinatorManager($row['coordinatorManager']);
						}

						$obj
							->setTime($row['time'])
							->setOpenProjects(intval($row['openProjects']))
							->setOpenTasks(intval($row['openTasks']))
							->setCoordinatorTasks(intval($row['coordinatorTasks']))
							->setRequestedQuotes(intval($row['requestedQuotes']))
							->setTotalAgreed(floatval($row['totalAgreed']))
							->setTotalCost(floatval($row['totalCost']))
							->setTotalWords(intval($row['totalWords']))
							->setTotalWorkingFiles(intval($row['totalWorkingFiles']));
						if (!$this->em->isOpen()) {
							$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
						}

						$this->em->persist($obj);
					} catch (\Throwable $thr) {
						$msg = "Error processing the Project {$project->getId()}.";
						$this->loggerSrv->addError($msg, $thr);
						$output->writeln($msg);
						continue;
					}
				}
			}
			$this->em->flush();
		}

		$output->writeln('PROCESSING FINISHED');

		return Command::SUCCESS;
	}
}
