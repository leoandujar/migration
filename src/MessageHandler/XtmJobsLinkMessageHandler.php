<?php

namespace App\MessageHandler;

use App\Command\Services\AlertBuilderService;
use App\Command\Services\Helper;
use App\Message\XtmJobsLinkMessage;
use App\Model\Entity\Activity;
use App\Model\Entity\Alert;
use App\Model\Entity\AnalyticsProject;
use App\Model\Entity\Category;
use App\Model\Entity\Task;
use App\Service\LoggerService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmJobsLinkMessageHandler
{
	private AlertBuilderService $alertBuilder;

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
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		AlertBuilderService $alertBuilder,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
		$this->alertBuilder = $alertBuilder;
	}

	public function __invoke(XtmJobsLinkMessage $message): void
	{
		$limit = $message->getLimit();
		$this->loggerSrv->addInfo('Linking Jobs with Analytics Projects (it may take a while)');

		$analyticsProjects = $this->em->getRepository(AnalyticsProject::class)->findForJobsProcessing($limit);
		if (!count($analyticsProjects)) {
			$this->loggerSrv->addInfo('No projects to link');

			return;
		}

		foreach ($analyticsProjects as $analyticsProject) {
			$result = $this->link(analyticsProject: $analyticsProject);
			if (null === $result) {
				continue;
			}

			$this->em->persist($analyticsProject);

			$result = Helper::resultToString($result);
			$this->loggerSrv->addWarning('Analytics Project: '.$result.'[Warning!]');
		}
	}

	public function link(AnalyticsProject $analyticsProject): mixed
	{
		$result = null;
		$message = null;
		$project = null;
		$translateJobs = [];
		$problematicTranslationJobs = false;
		$lqaAllowed = false;
		$editDistanceAllowed = false;
		$lastProvider = null;

		$this->loggerSrv->addInfo('Analytics Projects: '.$analyticsProject->getExternalId().
			'('.$analyticsProject->getTargetLanguageCode().'): '.$analyticsProject->getName());

		if (null === $analyticsProject->getTask()) {
			$project = $analyticsProject->getProject();
			if (null === $project) {
				$message = 'Analytics project have no matching project';
				$result = Helper::resultToString(Helper::IGNORED);

				$this->loggerSrv->addInfo($result.'[Warning!]'.$message);

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

			$this->loggerSrv->addInfo($result.'[Warning!]'.$message);

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

			$result = Helper::IGNORED;
			if ($lqaAllowed) {
				$result = Helper::NOT_FOUND;
				$result = Helper::resultToString($result);
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
				ksort($translateJobs);
			}
			$translateJob = array_shift($translateJobs);
			$analyticsProject->setJob($translateJob);
		}

		$analyticsProject->setProcessingStatus(AnalyticsProject::JOBS_PROCESSED);

		return $result;
	}
}
