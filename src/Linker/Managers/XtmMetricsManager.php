<?php

namespace App\Linker\Managers;

use App\Model\Entity\XtmMetrics;
use App\Command\Services\Helper;
use App\Model\Entity\Alert;
use App\Service\LoggerService;
use App\Connector\Xtm\XtmConnector;
use App\Model\Utils\ParameterHelper;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\Entity\AnalyticsProjectStep;
use App\Model\Repository\XtmMetricsRepository;
use App\Command\Services\AlertBuilderService;
use App\Model\Repository\FetchQueueRepository;
use App\Model\Repository\AnalyticsProjectRepository;
use App\Model\Repository\AnalyticsProjectStepRepository;

class XtmMetricsManager extends AbstractXtmManager
{
	private LoggerService $loggerSrv;
	protected $entityName = 'Metrics';
	private EntityManagerInterface $em;
	private XtmConnector $xtmConnector;
	private XtmMetricsRepository $metricsRepo;
	private AnalyticsProjectRepository $analyticsProjectRepo;
	private AnalyticsProjectStepRepository $analyticsProjectStepRepo;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		XtmConnector $xtmConnector,
		ParameterHelper $parameterHelper,
		ManagerRegistry $managerRegistry,
		AlertBuilderService $alertBuilder,
		XtmMetricsRepository $metricsRepository,
		FetchQueueRepository $queueRepository,
		AnalyticsProjectRepository $analyticsProjectRepository,
		AnalyticsProjectStepRepository $analyticsProjectStep,
	) {
		parent::__construct($em, $managerRegistry, $loggerSrv, $parameterHelper, $alertBuilder, $queueRepository, $xtmConnector);
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->xtmConnector = $xtmConnector;
		$this->metricsRepo = $metricsRepository;
		$this->analyticsProjectStepRepo = $analyticsProjectStep;
		$this->analyticsProjectRepo = $analyticsProjectRepository;
	}

	public function updateProjectMetrics(int $analyticsProjectId): ?array
	{
		$return = [];
		$metricsResponse = $this->xtmConnector->getMetricsByProjectId($analyticsProjectId);
		if (!$metricsResponse->isSuccessfull()) {
			$analyticsProjectEntities = $this->analyticsProjectRepo->findByExternalId($analyticsProjectId);
			foreach ($analyticsProjectEntities as $analyticsProjectEntity) {
				$fetchAttempts = $analyticsProjectEntity->getFetchAttempts() + 1;
				$analyticsProjectEntity->setFetchAttempts($fetchAttempts);
				$this->em->persist($analyticsProjectEntity);

				if ($fetchAttempts >= AnalyticsProjectRepository::MAX_FETCH_ATTEMPTS) {
					$this->alertBuilder->create()
						->setEntity($analyticsProjectEntity)
						->setType(Alert::T_ACTION_NEEDED)
						->setDescription('Impossible to fetch metrics data for Analytics project within '.
							AnalyticsProjectRepository::MAX_FETCH_ATTEMPTS.' attempts')
						->save();
				}
			}
			$this->em->flush();

			return ['All' => Helper::NOT_FOUND];
		}

		$metricsData = $metricsResponse->getData();

		foreach ($metricsData as $targetLanguageCode => $metrics) {
			$analyticsProjectEntity = $this->analyticsProjectRepo->findOneByExternalIdAndLanguage($analyticsProjectId, $targetLanguageCode);
			if (null === $analyticsProjectEntity) {
				$this->loggerSrv->addError("No such project in database $analyticsProjectId");

				return null;
			}
			$this->entity = $this->metricsRepo->findByAnalyticsProjectAndLang($analyticsProjectEntity->getId(), $targetLanguageCode);

			if (null === $this->entity) {
				$this->entity = new XtmMetrics();
				$this->entity->setAnalyticsProject($analyticsProjectEntity)
					->setTargetLanguageCode($targetLanguageCode)
					->setExternalId(intval($analyticsProjectId));
				$this->entityCreated = true;
			} else {
				$this->entityCreated = false;
			}
			// i'm lazy and i bet it'll fail one day when structure will change
			foreach ($metrics['coreMetrics'] as $key => $val) {
				$command = 'set'.ucfirst($key);
				if (method_exists($this->entity, $command)) {
					$this->entity->$command(strval($val));
				}
			}

			$this->em->persist($this->entity);

			if (!is_array($metrics['metricsProgress'])) {
				$metrics['metricsProgress'] = [$metrics['metricsProgress']];
			}

			foreach ($metrics['metricsProgress'] as $name => $step) {
				$stepOrdinal = array_search($name, array_keys($metrics['metricsProgress']));
				$stepEntity = $this->analyticsProjectStepRepo->findByProjectLangAndName($analyticsProjectEntity, $targetLanguageCode, strval($name));
				if (null === $stepEntity) {
					$stepEntity = new AnalyticsProjectStep();
					$stepEntity->setAnalyticsProject($analyticsProjectEntity)
						->setName(strval($name))
						->setTargetLanguageCode($targetLanguageCode);
				}
				$stepEntity->setOrdinal($stepOrdinal)
					->setTargetLanguageTag($analyticsProjectEntity->getTargetLanguageTag());

				$this->em->persist($stepEntity);
			}

			$analyticsProjectEntity->setProcessingStatus(AnalyticsProject::METRICS_PROCESSED);
			$analyticsProjectEntity->setFetchAttempts(0);
			$this->em->persist($analyticsProjectEntity);
			$return[$targetLanguageCode] = ($this->entityCreated ? Helper::CREATED : Helper::UPDATED);
		}

		if ($this->isQueueUsed()) {
			$this->removeFromQueue($this->entity->getExternalId(), false);
		}

		$this->em->flush();
		$this->em->detach($this->entity);

		return $return;
	}
}
