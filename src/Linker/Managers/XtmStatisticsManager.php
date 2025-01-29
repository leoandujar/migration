<?php

namespace App\Linker\Managers;

use App\Model\Entity\XtmStatistics;
use App\Command\Services\Helper;
use App\Model\Entity\Alert;
use App\Service\LoggerService;
use App\Connector\Xtm\XtmConnector;
use App\Model\Utils\ParameterHelper;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Command\Services\AlertBuilderService;
use App\Model\Repository\FetchQueueRepository;
use App\Model\Repository\XtmStatisticsRepository;
use App\Connector\XtmConnector as OldXtmConnector;
use App\Model\Repository\AnalyticsProjectRepository;

class XtmStatisticsManager extends AbstractXtmManager
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	protected $entityName = 'Statistics';
	private OldXtmConnector $xtmConnector;
	private AnalyticsProjectRepository $repository;
	private XtmStatisticsRepository $statisticsRepository;

	public const MAX_CONNECTION_FAILURES = 2;

	public function __construct(
		EntityManagerInterface $em,
		ManagerRegistry $managerRegistry,
		LoggerService $loggerSrv,
		ParameterHelper $parameterHelper,
		AlertBuilderService $alertBuilder,
		XtmConnector $connector,
		FetchQueueRepository $queueRepository,
		AnalyticsProjectRepository $repository,
		OldXtmConnector $xtmConnector,
		XtmStatisticsRepository $statisticsRepository
	) {
		parent::__construct($em, $managerRegistry, $loggerSrv, $parameterHelper, $alertBuilder, $queueRepository, $connector);
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->repository = $repository;
		$this->xtmConnector = $xtmConnector;
		$this->statisticsRepository = $statisticsRepository;
	}

	/**
	 * @return array
	 */
	public function updateProjectStatistics(int $analyticsProjectId)
	{
		$return = [];
		$failureCounter = 0;

		do {
			$statisticsDataResponse = $this->connector->getStatsByProjectId($analyticsProjectId);
			if ($statisticsDataResponse->isSuccessfull() && !count($statisticsDataResponse->getData())) {
				++$failureCounter;
				time_nanosleep(0, 250000000);
			}
		} while ($statisticsDataResponse->isSuccessfull() && !count($statisticsDataResponse->getData()) && $failureCounter < self::MAX_CONNECTION_FAILURES);

		$analyticsProjectEntities = $this->repository->findByExternalId($analyticsProjectId);

		if (!$statisticsDataResponse->isSuccessfull()) {
			foreach ($analyticsProjectEntities as $analyticsProjectEntity) {
				$fetchAttempts = $analyticsProjectEntity->getFetchAttempts() + 1;
				$analyticsProjectEntity->setFetchAttempts($fetchAttempts);
				$this->em->persist($analyticsProjectEntity);

				if ($fetchAttempts >= AnalyticsProjectRepository::MAX_FETCH_ATTEMPTS) {
					$this->alertBuilder->create()
						->setEntity($analyticsProjectEntity)
						->setType(Alert::T_ACTION_NEEDED)
						->setDescription('Impossible to fetch statistics data for Analytics project within '.
							AnalyticsProjectRepository::MAX_FETCH_ATTEMPTS.' attempts')
						->save();
				}
			}
			$this->em->flush();

			return ['ALL' => Helper::NOT_FOUND];
		}

		$statisticsData = $statisticsDataResponse->getData();
		if (!count($statisticsData)) {
			foreach ($analyticsProjectEntities as $analyticsProjectEntity) {
				$analyticsProjectEntity->setProcessingStatus(AnalyticsProject::STATISTICS_PROCESSED);
				$this->em->persist($analyticsProjectEntity);
				$targetLanguage = $analyticsProjectEntity->getTargetLanguageCode();
				$return[$targetLanguage] = Helper::IGNORED;
			}

			return $return;
		}

		foreach ($statisticsData as $targetLanguage => $statisticsDatum) {
			foreach ($statisticsDatum['usersStatistics'] as $usersStatistics) {
				$analyticsProjectEntity = $this->repository->findOneByExternalIdAndLanguage($analyticsProjectId, $targetLanguage);
				if (null === $analyticsProjectEntity) {
					$this->loggerSrv->addError("No such project in database $analyticsProjectId");

					return [];
				}

				$stepsObjects = $analyticsProjectEntity->getAnalyticsProjectSteps();
				$steps = [];
				foreach ($stepsObjects as $step) {
					$stepName = $step->getName();
					$steps[$stepName] = $step;
				}

				if (!isset($usersStatistics['stepsStatistics'])) {
					$alertMessage = 'There are no step statistics for Analytics Project '.
						$analyticsProjectEntity->getProjectHumanId().' ('.$analyticsProjectEntity->getExternalId().')';
					$this->loggerSrv->addWarning($alertMessage);
					$this->alertBuilder->create()
						->setEntity($analyticsProjectEntity)
						->setType(Alert::T_ATTENTION_NEEDED)
						->setDescription($alertMessage)
						->save();
					$fetchAttempts = $analyticsProjectEntity->getFetchAttempts() + 1;
					$analyticsProjectEntity->setFetchAttempts($fetchAttempts);
					$this->em->persist($analyticsProjectEntity);
					$return[$targetLanguage] = Helper::IGNORED;
					continue;
				}

				if (!is_array($usersStatistics['stepsStatistics'])) {
					$usersStatistics['stepsStatistics'] = [$usersStatistics['stepsStatistics']];
				}

				foreach ($usersStatistics['stepsStatistics'] as $statisticsStepData) {
					if (!isset($statisticsStepData['workflowStepName'])) {
						$alertMessage = 'One of the steps for Analytics Project '.
							$analyticsProjectEntity->getProjectHumanId().' ('.$analyticsProjectEntity->getExternalId().
							') have no name!';
						$this->loggerSrv->addWarning($alertMessage);
						$this->alertBuilder->create()
							->setEntity($analyticsProjectEntity)
							->setType(Alert::T_ATTENTION_NEEDED)
							->setDescription($alertMessage)
							->save();
						continue;
					}

					$stepName = strval($statisticsStepData['workflowStepName']);
					if (!isset($steps[$stepName])) {
						$alertMessage = 'Step '.$stepName.' for Analytics Project '.
							$analyticsProjectEntity->getProjectHumanId().' ('.$analyticsProjectEntity->getExternalId().
							') is missing!';
						$this->loggerSrv->addWarning($alertMessage);
						$this->alertBuilder->create()
							->setEntity($analyticsProjectEntity)
							->setType(Alert::T_ATTENTION_NEEDED)
							->setDescription($alertMessage)
							->save();
						continue;
					}

					$sourceStepStatistics = $this->statisticsRepository->findByStepAndType($steps[$stepName], XtmStatistics::T_SOURCE);

					if (null === $sourceStepStatistics) {
						$sourceStepStatistics = new XtmStatistics();
						$sourceStepStatistics->setStep($steps[$stepName])
							->setType(XtmStatistics::T_SOURCE);
						$this->entityCreated = true;
					} else {
						$this->entityCreated = false;
					}
					// i'm lazy and i bet it'll fail one day when structure will change
					$sourceStats = $statisticsStepData['jobsStatistics'] ?? [];
					$sourceStats = count($sourceStats) ? array_shift($sourceStats) : [];
					foreach ($sourceStats['sourceStatistics'] as $key => $val) {
						$command = 'set'.ucfirst($key);
						if (method_exists($sourceStepStatistics, $command)) {
							$sourceStepStatistics->$command(strval($val));
						}
					}

					$this->em->persist($sourceStepStatistics);

					$targetStepStatistics = $this->statisticsRepository->findByStepAndType($steps[$stepName], XtmStatistics::T_TARGET);

					if (null === $targetStepStatistics) {
						$targetStepStatistics = new XtmStatistics();
						$targetStepStatistics->setStep($steps[$stepName])
							->setType(XtmStatistics::T_TARGET);
						$this->entityCreated = true;
					} else {
						$this->entityCreated = false;
					}
					// i'm lazy and i bet it'll fail one day when structure will change
					foreach ($sourceStats['targetStatistics'] as $key => $val) {
						$command = 'set'.ucfirst($key);
						if (method_exists($targetStepStatistics, $command)) {
							$targetStepStatistics->$command(strval($val));
						}
					}

					$this->em->persist($targetStepStatistics);
				}

				$analyticsProjectEntity->setProcessingStatus(AnalyticsProject::STATISTICS_PROCESSED);
				$analyticsProjectEntity->setFetchAttempts(0);
				$this->em->persist($analyticsProjectEntity);
				$return[$targetLanguage] = ($this->entityCreated ? Helper::CREATED : Helper::UPDATED);
			}
		}

		foreach ($analyticsProjectEntities as $analyticsProjectEntity) {
			$targetLanguage = $analyticsProjectEntity->getTargetLanguageCode();
			if (!isset($return[$targetLanguage])) {
				$alertMessage = 'There are no step statistics for Analytics Project '.
					$analyticsProjectEntity->getProjectHumanId().' ('.$analyticsProjectEntity->getExternalId().
					') language '.$targetLanguage;
				$this->loggerSrv->addWarning($alertMessage);
				$this->alertBuilder->create()
					->setEntity($analyticsProjectEntity)
					->setType(Alert::T_ATTENTION_NEEDED)
					->setDescription($alertMessage)
					->save();
				$fetchAttempts = $analyticsProjectEntity->getFetchAttempts() + 1;
				$analyticsProjectEntity->setFetchAttempts($fetchAttempts);
				$this->em->persist($analyticsProjectEntity);
				$return[$targetLanguage] = Helper::IGNORED;
			}
		}

		$this->em->flush();

		return $return;
	}

	public function getProjectStatistics(int $projectId): mixed
	{
		$statistics = $this->xtmConnector->getProjectStatistics($projectId);

		return $statistics->return->projectStatistics;
	}
}
