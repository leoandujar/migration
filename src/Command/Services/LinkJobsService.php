<?php

namespace App\Command\Services;

use App\Model\Entity\Task;
use App\Model\Entity\Alert;
use App\Service\LoggerService;
use App\Model\Entity\Activity;
use App\Model\Entity\Category;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Console\Input\InputInterface;
use App\Model\Repository\AnalyticsProjectRepository;
use Symfony\Component\Console\Output\OutputInterface;

class LinkJobsService
{
	private AlertBuilderService $alertBuilder;
	private AnalyticsProjectRepository $repository;

	/**
	 * @var array
	 */
	private $translateExcludeJobNames = [
		'translation_payment',
		'TS Payment',
		'TS Claims Translation',
		'TS CVCP billing',
		'Translation Review',
		'Updating of files',
		'TS Payment',
		'24 hour TAT (minimum job rate)',
		'Revision',
		'Quality Assurance',
		'Creation of Babies',
		'Translation TS billing',
		'TS Claims Billing',
		'ts payment',
		'Translation Corrections',
		'Transcreation Report',
		'Translation Billing',
		'Translation Review',
		'TS Payment',
		'Translation billing',
		'TS PAYMENT',
		'Edition',
		'TS payment',
		'Alt. Text',
		'Verification',
		'Translation biling',
		'Proof Reading',
		'Translation payabe',
		'Hispano Payment',
		'TS Claims billing',
		'TS payment',
		'Payment: Translation and Editing',
	];

	private LoggerService $loggerSrv;

	private EntityManagerInterface $em;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		AlertBuilderService $alertBuilder,
		AnalyticsProjectRepository $repository
	) {
		$this->em = $em;
		$this->repository = $repository;
		$this->alertBuilder = $alertBuilder;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function execute(InputInterface $input, OutputInterface $output): void
	{
		$analyticsProjects = $this->repository->findForJobsProcessing($input->getOption('limit'));
		if (!count($analyticsProjects)) {
			$output->write('No projects to link');
		}

		foreach ($analyticsProjects as $analyticsProject) {
			$result = $this->link($analyticsProject, $output);
			if (null === $result) {
				continue;
			}

			$this->em->persist($analyticsProject);

			$result = Helper::resultToString($result);
			$output->writeln('<entval>'.$result.'</entval>'.(empty($message) ? '' : ' [<warning>Warning!</warning>]'.$message));
			$this->loggerSrv->addWarning('Analytics Project: '.$result.(empty($message) ? '' : ' [Warning!]'.$message));
		}
		$this->em->flush();
	}

	public function link(AnalyticsProject $analyticsProject, OutputInterface $output = null): mixed
	{
		$result = null;
		$message = null;
		$project = null;
		$translateJobs = [];
		$problematicTranslationJobs = false;
		$lqaAllowed = false;
		$editDistanceAllowed = false;
		$lastProvider = null;
		if (null !== $output) {
			$output->write('Analytics Project <entname>'.$analyticsProject->getExternalId().'</entname> '.
				$analyticsProject->getTargetLanguageCode().' (<entname>'.$analyticsProject->getName().
				'</entname>): ');
		}
		$this->loggerSrv->addInfo('Analytics Projects: '.$analyticsProject->getExternalId().
			'('.$analyticsProject->getTargetLanguageCode().'): '.$analyticsProject->getName());

		if (null === $analyticsProject->getTask()) {
			$project = $analyticsProject->getProject();
			if (null === $project) {
				$message = 'Analytics project have no matching project';
				$result = Helper::resultToString(Helper::IGNORED);
				if (null !== $output) {
					$output->writeln('<entval>'.$result.'</entval> <warning>[Warning!]</warning> '.$message);
				}
				$this->loggerSrv->addWarning('Analytics Project: '.$analyticsProject->getExternalId().': '.$result.'[Warning!]'.$message);

				return null;
			}
			$criteria = Criteria::create()->where(Criteria::expr()->eq('targetLanguageTag', $analyticsProject->getTargetLanguageTag()));
			$tasks = $project->getTasks()->matching($criteria);
		} else {
			$tasks = [$analyticsProject->getTask()];
		}

		if (empty($tasks)) {
			$message = 'Project have no matching tasks';
			$this->alertBuilder->create()
				->setType(Alert::T_ATTENTION_NEEDED)
				->setDescription($message);
			if (null === $project) {
				$this->alertBuilder->setEntity($analyticsProject);
			} else {
				$this->alertBuilder->setEntity($project);
			}
			$this->alertBuilder->save();

			$result = Helper::resultToString(Helper::IGNORED);
			if (null !== $output) {
				$output->writeln('<entval>'.$result.'</entval> <warning>[Warning!]</warning> '.$message);
			}
			$this->loggerSrv->addWarning('Analytics Project: '.$analyticsProject->getExternalId().': '.$result.'[Warning!]'.$message);

			return null;
		}

		foreach ($tasks as $task) {
			/** @var Task $task */
			$categories = $task->getCategories();
			/** @var Category $category */
			foreach ($categories as $category) {
				if (Category::CATEGORY_LQA === $category->getName()) {
					$lqaAllowed = true;
				}
				if (Category::CATEGORY_MT === $category->getName()) {
					$editDistanceAllowed = true;
					break;
				}
			}

			$analyticsProject->setLqaAllowed($lqaAllowed);
			$analyticsProject->setEditDistanceAllowed($editDistanceAllowed);

			$jobs = $task->getActivities();
			foreach ($jobs as $job) {
				/** @var Activity $job */
				if (null === $job->getActivityType()) {
					$result = Helper::IGNORED;
					$message = 'Job have no JobType assigned';
					$this->alertBuilder->create()
						->setType(Alert::T_ACTION_NEEDED)
						->setDescription($message)
						->setEntity($job)
						->save();

					$result = Helper::resultToString($result);
					if (null !== $output) {
						$output->writeln('<entval>'.$result.'</entval>'.(empty($message) ? '' : ' [<warning>Warning!</warning>]'.$message));
					}
					$this->loggerSrv->addWarning('Analytics Project: '.$result.'[Warning!]'.$message);
					break;
				}

				// Ignore cancelled jobs
				if ('Cancelled' == $job->getStatus()) {
					continue;
				}
				$isTranslation = str_contains($job->getActivityType()->getName(), 'Translation');
				if (
					false === array_search($job->getActivityName(), $this->translateExcludeJobNames)
					&& $isTranslation
				) {
					if (
						!empty($translateJobs)
						&& null !== $lastProvider
						&& $job->getProvider() !== $lastProvider
					) {
						$problematicTranslationJobs = true;
						continue;
					}
					$translateJobs[$job->getProjectPhaseIdNumber()] = $job;
					$lastProvider = $job->getProvider();
				}
			}
			if (isset($result) && is_string($result)) {
				break;
			}
		}
		if (isset($result) && is_string($result)) {
			return null;
		}

		if ($problematicTranslationJobs) {
			if ($lqaAllowed) {
				$message = 'There is more than one translation job for LQA belonging to different Providers!';
			} else {
				$message = 'There is more than one translation job belonging to different Providers!';
			}
			$this->alertBuilder->create()
				->setType(Alert::T_ACTION_NEEDED)
				->setDescription($message);
			if (null === $project) {
				$this->alertBuilder->setEntity($task);
			} else {
				$this->alertBuilder->setEntity($project);
			}
			$this->alertBuilder->save();

			// Only stop processing for LQA entities
			$result = Helper::IGNORED;
			if ($lqaAllowed) {
				$result = Helper::NOT_FOUND;
				$result = Helper::resultToString($result);
				if (null !== $output) {
					$output->writeln('<entval>'.$result.'</entval>'.(empty($message) ? '' : ' [<warning>Warning!</warning>]'.$message));
				}
				$this->loggerSrv->addWarning('Analytics Project: '.$result.'[Warning!]'.$message);

				return null;
			}
		} elseif (empty($translateJobs)) {
			if (empty($result)) {
				$result = Helper::NOT_FOUND;
			}
		} else {
			if (empty($result)) {
				$result = Helper::UPDATED;
			}
			$translateJobsNumber = count($translateJobs);
			if ($translateJobsNumber > 1) {
				$message = 'There is more than one translation job for the same Provider!';
				// We want first one with the lowest Human ID
				ksort($translateJobs);
			}
			$translateJob = array_shift($translateJobs);
			$analyticsProject->setJob($translateJob);
		}

		$analyticsProject->setProcessingStatus(AnalyticsProject::JOBS_PROCESSED);

		return $result;
	}
}
