<?php

namespace App\MessageHandler;

use App\Message\TimescaleMessage;
use App\Model\Entity\Project;
use App\Model\Entity\Quote;
use App\Model\Entity\Task;
use App\Model\Entity\TimeSerieStats;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TimescaleMessageHandler
{
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
	}

	/**
	 * @throws OptimisticLockException
	 * @throws ORMException
	 */
	public function __invoke(TimescaleMessage $message)
	{
		$projects = $this->em->getRepository(Project::class)->getOpenedProjectsByBranch('1');
		$data = [];
		foreach ($projects as $key => $project) {
			try {
				$customer = $project->getCustomer();
				$manager = $project->getProjectManager();
				$coordinator = $project->getProjectCoordinator();
				$totalWordsSqlResult = $this->em->getRepository(Task::class)->getTotalWordsByProject($project->getId());
				$workingFilesSqlResult = $this->em->getRepository(Task::class)->getTotalWorkingFilesByProject($project->getId());

				if ($customer) {
					$customerOpenProjs = $this->em->getRepository(Project::class)->getOpenedProjectsByEntity($customer?->getId());
					$customerOpenTasks = $this->em->getRepository(Task::class)->getOpenedTaskByEntity($customer?->getId());
					$customerCoordinatorTasks = $this->em->getRepository(Task::class)->getCoordinatorTaskByEntity($customer?->getId());
					$customerRequestedQuotes = $this->em->getRepository(Quote::class)->getRequestedByEntity($customer?->getId());
					$customerAgreedCost = $this->em->getRepository(Project::class)->getTotalAgreedAndCostEntity($customer?->getId());
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
					$managerOpenProjs = $this->em->getRepository(Project::class)->getOpenedProjectsByEntity(null, $manager?->getId());
					$managerOpenTasks = $this->em->getRepository(Task::class)->getOpenedTaskByEntity(null, $manager?->getId());
					$managerCoordinatorTasks = $this->em->getRepository(Task::class)->getCoordinatorTaskByEntity(null, $manager?->getId());
					$managerRequestedQuotes = $this->em->getRepository(Quote::class)->getRequestedByEntity(null, $manager?->getId());
					$managerAgreedCost = $this->em->getRepository(Project::class)->getTotalAgreedAndCostEntity(null, $manager?->getId());
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
					$coordinatorOpenProjs = $this->em->getRepository(Project::class)->getOpenedProjectsByEntity(null, null, $coordinator?->getId());
					$coordinatorOpenTasks = $this->em->getRepository(Task::class)->getOpenedTaskByEntity(null, null, $coordinator?->getId());
					$coordinatorRequestedQuotes = $this->em->getRepository(Quote::class)->getRequestedByEntity(null, null, $coordinator?->getId());
					$coordinatorAgreedCost = $this->em->getRepository(Project::class)->getTotalAgreedAndCostEntity(null, null, $coordinator?->getId());
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
						$msg = 'Error processing the Project:'.isset($project) ? $project->getId() : 'No project.';
						$this->loggerSrv->addError($msg, $thr);
						continue;
					}
				}
			}
			$this->em->flush();
		}
	}
}
