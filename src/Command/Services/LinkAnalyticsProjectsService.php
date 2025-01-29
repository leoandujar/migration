<?php

namespace App\Command\Services;

use App\Model\Entity\Task;
use App\Model\Entity\Alert;
use App\Service\LoggerService;
use App\Model\Entity\XtrfLanguage;
use App\Model\Entity\AnalyticsProject;
use App\Model\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\ProjectRepository;
use Symfony\Component\Console\Input\InputInterface;
use App\Model\Repository\AnalyticsProjectRepository;
use Symfony\Component\Console\Output\OutputInterface;

class LinkAnalyticsProjectsService
{
	private AlertBuilderService $alertBuilder;
	private AnalyticsProjectRepository $analyticsProjectRepository;
	private TaskRepository $taskRepository;
	private ProjectRepository $projectRepository;
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		AlertBuilderService $alertBuilder,
		TaskRepository $taskRepository,
		ProjectRepository $projectRepository,
		AnalyticsProjectRepository $analyticsProjectRepository
	) {
		$this->em = $em;
		$this->alertBuilder = $alertBuilder;
		$this->analyticsProjectRepository = $analyticsProjectRepository;
		$this->taskRepository = $taskRepository;
		$this->projectRepository = $projectRepository;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function execute(InputInterface $input, OutputInterface $output): void
	{
		$analyticsProjects = $this->analyticsProjectRepository->findForProcessing($input->getOption('limit'));
		if (!count($analyticsProjects)) {
			$output->write('No projects to link');
		}
		$i = 0;
		foreach ($analyticsProjects as $analyticsProject) {
			$targetLanguageTag = $analyticsProject->getTargetLanguageTag();
			$output->write('Analytics Project <entname>'.$analyticsProject->getExternalId().'</entname> ');
			if (empty($targetLanguageTag)) {
				$output->write('<warning>[Warning!] No target language</warning>');
				$this->loggerSrv->addInfo('Analytics Project '.$analyticsProject->getExternalId().': [Warning!] No target language');
			} else {
				$output->write($targetLanguageTag->getSymbol());
				$this->loggerSrv->addInfo('Analytics Project '.$analyticsProject->getExternalId().': '.$targetLanguageTag->getSymbol());
			}
			$output->write(' (<entname>'.$analyticsProject->getName().'</entname>): ');
			$result = $this->link($analyticsProject);
			$this->em->persist($analyticsProject);
			++$i;
			if ($i > 100) {
				$this->em->flush();
				$i = 0;
			}

			$result = Helper::resultToString($result);
			$output->writeln('<entval>'.$result.'</entval>'.(empty($message) ? '' : ' [<warning>Warning!</warning>]'.$message));
			$this->loggerSrv->addInfo($result.(empty($message) ? '' : ' [Warning!]'.$message));
		}

		if ($i > 0) {
			$this->em->flush();
		}
	}

	public function link(AnalyticsProject &$analyticsProject): int
	{
		$name = $analyticsProject->getName();
		$name = preg_replace('/(\s-\s)+([0-9]+)/', '-\\2', strtr($name, ['_' => ' ', '(' => ' ']));
		$name = preg_replace('/(\s-\s)+(\w*[0-9]{6,8}(-[0-9]+)+)/', '-\\2', $name);
		$spacePos = strpos($name, ' ');
		if (false !== $spacePos) {
			$name = substr($name, 0, $spacePos);
		}
		$dupPos = strpos($name, '_');
		if (false !== $dupPos) {
			$name = substr($name, 0, $dupPos);
		}
		$name = trim($name);
		$subName = trim(preg_replace('/(.*-)([0-9]{6,8}(-[0-9]+)+)/', '\\2', $name));

		$levelNumber = substr_count($subName, '-');

		if (2 == $levelNumber) {
			$projectName = substr($name, 0, strrpos($name, '-'));
			$analyticsProject->setProjectHumanId($projectName);
			$task = $this->taskRepository->findOneBy(['projectPhaseIdNumber' => $name]);
			if (null === $task) {
				$analyticsProject->setIgnored(true);
				$result = Helper::NOT_FOUND;
			} elseif (null === $task->getProject()) {
				$analyticsProject->setTask($task)->setIgnored(true);
				$result = Helper::NOT_FOUND;
			} else {
				$analyticsProject->setTask($task)
					->setProject($task->getProject())
					->setProcessingStatus(AnalyticsProject::LINKED);
				$result = Helper::UPDATED;
			}
		} elseif (1 == $levelNumber) {
			$analyticsProject->setProjectHumanId($name);
			$project = $this->projectRepository->findOneBy(['idNumber' => $name]);
			if (null === $project) {
				$analyticsProject->setIgnored(true);
				$result = Helper::NOT_FOUND;
			} else {
				$tasks = $project->getTasks();
				if (null === $tasks) {
					$analyticsProject->setIgnored(true);
					$result = Helper::NOT_FOUND;
					$message = 'Project found, but have no tasks';
					$this->alertBuilder->create()
						->setEntity($analyticsProject)
						->setType(Alert::T_ATTENTION_NEEDED)
						->setDescription($message)
						->save();
				} else {
					$taskFound = false;
					$potentialTask = [];
					/** @var Task $val */
					foreach ($tasks as $val) {
						$taskLanguageTag = $val->getTargetLanguage();
						$analyticsProjectLanguage = $analyticsProject->getTargetLanguageTag();
						if ($taskLanguageTag === $analyticsProjectLanguage) {
							$taskFound = true;
							$analyticsProject->setTask($val);
						} elseif (
							$taskLanguageTag instanceof XtrfLanguage
							&& $taskLanguageTag->getLanguageCode() == $analyticsProjectLanguage->getLanguageCode()
							&& $taskLanguageTag->getCountryCode() == $analyticsProjectLanguage->getCountryCode()
							&& null === $taskLanguageTag->getScript()
						) {
							$potentialTask[] = $val;
						} elseif (
							$taskLanguageTag instanceof XtrfLanguage
							&& $taskLanguageTag->getLanguageCode() == $analyticsProjectLanguage->getLanguageCode()
							&& null === $taskLanguageTag->getCountryCode()
						) {
							$potentialTask[] = $val;
						}
					}
					if (!$taskFound && !empty($potentialTask) && 1 == count($potentialTask)) {
						$taskFound = true;
						$analyticsProject->setTask($potentialTask[0]);
						$message = 'Project found, but language '.$analyticsProject->getTargetLanguageTag()->getLangiso().
							' was conditionally matched to Task with language '.$potentialTask[0]->getTargetLanguage()->getLangiso();
						$this->alertBuilder->create()
							->setEntity($analyticsProject)
							->setType(Alert::T_ATTENTION_NEEDED)
							->setDescription($message)
							->save();
					}
					if (!$taskFound) {
						$analyticsProject->setIgnored(true);
						$result = Helper::NOT_FOUND;
						if (0 == count($potentialTask)) {
							$message = 'Project found, but Task for language '.$analyticsProject->getTargetLanguageTag()->getLangiso().' not found';
							$this->alertBuilder->create()
								->setEntity($analyticsProject)
								->setType(Alert::T_ATTENTION_NEEDED)
								->setDescription($message)
								->save();
						} else {
							$message = 'Project found, but there is more than one potentially matching tasks for language '.$analyticsProject->getTargetLanguageTag()->getLangiso();
							$this->alertBuilder->create()
								->setEntity($analyticsProject)
								->setType(Alert::T_ACTION_NEEDED)
								->setDescription($message)
								->save();
						}
					} else {
						$analyticsProject->setProject($project)
							->setProcessingStatus(AnalyticsProject::LINKED);
						$result = Helper::UPDATED;
					}
				}
			}
		} else {
			$analyticsProject->setIgnored(true);
			$result = Helper::IGNORED;
		}

		return $result;
	}
}
