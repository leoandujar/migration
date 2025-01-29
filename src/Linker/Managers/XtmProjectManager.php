<?php

namespace App\Linker\Managers;

use App\Command\Services\Helper;
use App\Service\LoggerService;
use App\Model\Utils\LanguageHelper;
use App\Connector\Xtm\XtmConnector;
use App\Model\Utils\ParameterHelper;
use App\Model\Entity\AnalyticsProject;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Command\Services\AlertBuilderService;
use App\Model\Entity\XtrfLanguage;
use App\Model\Repository\FetchQueueRepository;
use App\Model\Repository\LanguageTagRepository;
use App\Model\Repository\AnalyticsProjectRepository;

class XtmProjectManager extends AbstractXtmManager
{
	/* @var $entity AnalyticsProject */
	/**
	 * @var string
	 */
	protected $entityName = 'AnalyticsProject';

	protected $projectData;

	private $pages;

	private $entities;

	private $languageHelper;
	/**
	 * @var AnalyticsProjectRepository
	 */
	private $repository;
	/**
	 * @var LanguageTagRepository
	 */
	private $tagRepository;
	/**
	 * @var EntityManagerInterface
	 */
	private $em;
	/**
	 * @var LoggerService
	 */
	private $loggerSrv;

	public function __construct(
		EntityManagerInterface $em,
		ManagerRegistry $managerRegistry,
		LoggerService $loggerSrv,
		ParameterHelper $parameterHelper,
		AlertBuilderService $alertBuilder,
		XtmConnector $connector,
		FetchQueueRepository $queueRepository,
		LanguageHelper $languageHelper,
		AnalyticsProjectRepository $repository,
		LanguageTagRepository $tagRepository,
	) {
		parent::__construct($em, $managerRegistry, $loggerSrv, $parameterHelper, $alertBuilder, $queueRepository, $connector);
		$this->languageHelper = $languageHelper;
		$this->repository = $repository;
		$this->tagRepository = $tagRepository;
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
	}

	/**
	 * @param int $analyticsProjectId
	 *
	 * @return array|array[]
	 */
	public function updateAnalyticsProject($analyticsProjectId = null)
	{
		$analyticsProjectId = $analyticsProjectId ?? intval($this->projectData['id']);
		$response = $this->connector->getProjectById($analyticsProjectId);

		if (null === $response || !$response->isSuccessfull()) {
			$this->loggerSrv->addWarning('Unable to retrieve the project '.$analyticsProjectId.' ('.$this->projectData['name'].')');

			return ['all' => ['status' => Helper::IGNORED]];
		}

		if (null === $this->projectData) {
			$this->projectData = [
				'status' => AnalyticsProject::S_FINISHED,
			];
		}

		$this->projectData = array_merge($this->projectData, $response->getProjectData());
		$return = [];
		if (!isset($this->projectData['targetLanguages'])) {
			$this->loggerSrv->addWarning('There are no target languages set for the Analytics Project '.$analyticsProjectId.' ('.$this->projectData['name'].')');

			return ['all' => ['status' => Helper::IGNORED]];
		}
		if (!is_array($this->projectData['targetLanguages'])) {
			$this->projectData['targetLanguages'] = [$this->projectData['targetLanguages']];
		}

		$entities = [];
		if (!empty($this->entities)) {
			foreach ($this->entities as $analyticsProject) {
				/* @var AnalyticsProject $analyticsProject */
				$targetLanguage = $analyticsProject->getTargetLanguageCode();
				if (false === array_search($targetLanguage, $this->projectData['targetLanguages'])) {
					$entities[$targetLanguage] = $analyticsProject;
				} else {
					$this->em->remove($analyticsProject);
					$return[$targetLanguage] = ['status' => Helper::DELETED];
				}
			}
		}

		foreach ($this->projectData['targetLanguages'] as $languageTagCode) {
			if (isset($entities[$languageTagCode])) {
				$this->entity = $entities[$languageTagCode];
			} else {
				$this->findProject($analyticsProjectId, $languageTagCode);
			}

			if (null === $this->entity) {
				$languageTagEntity = $this->findLanguageTag($languageTagCode);
				if (empty($languageTagEntity)) {
					$return[$languageTagCode] =
						['status' => Helper::IGNORED,
							'message' => '['.$this->projectData['name'].'] Matching language '.
								$languageTagCode.' was not found'];
					continue;
				}

				$this->entity = new AnalyticsProject();
				$this->entity->setExternalId($analyticsProjectId)
					->setTargetLanguageTag($languageTagEntity)
					->setTargetLanguageCode($languageTagCode);
				$return[$languageTagCode] = ['status' => Helper::CREATED];
			} elseif (
				null === $this->entity->getCreateDate()
			) {
				$this->entity
					->setCreateDate(isset($this->projectData['createDate']) ? $this->convertUnixDate($this->projectData['createDate']) : null)
					->setStartDate(isset($this->projectData['startDate']) ? $this->convertUnixDate($this->projectData['startDate']) : null)
					->setFinishDate(isset($this->projectData['finishDate']) ? $this->convertUnixDate($this->projectData['finishDate']) : null)
					->setDueDate(isset($this->projectData['dueDate']) ? $this->convertUnixDate($this->projectData['dueDate']) : null);
				continue;
			} elseif (
				$this->entity->getName() == strval($this->projectData['name'])
				&& $this->entity->getActivity() == strval($this->projectData['activity'])
				&& $this->entity->getStatus() == strval($this->projectData['status'])
			) {
				$return[$languageTagCode] = [
					'status' => Helper::NOT_CHANGED,
					'entity' => $this->entity,
				];
				continue;
			} else {
				$return[$languageTagCode] = ['status' => Helper::UPDATED];
			}

			$this->entity->setName(strval($this->projectData['name']))
				->setCreateDate(isset($this->projectData['createDate']) ? $this->convertUnixDate($this->projectData['createDate']) : null)
				->setStartDate(isset($this->projectData['startDate']) ? $this->convertUnixDate($this->projectData['startDate']) : null)
				->setFinishDate(isset($this->projectData['finishDate']) ? $this->convertUnixDate($this->projectData['finishDate']) : null)
				->setDueDate(isset($this->projectData['dueDate']) ? $this->convertUnixDate($this->projectData['dueDate']) : null)
				->setActivity(strval($this->projectData['activity']))
				->setStatus(strval($this->projectData['status']))
				->setIgnored(false)
				->setFetchAttempts(0)
				->setProcessingStatus(AnalyticsProject::CREATED);

			$language = $this->languageHelper->findLanguage($languageTagCode);
			if (null !== $language) {
				$this->entity->setTargetLanguage($language);
			}
			$return[$languageTagCode]['entity'] = $this->entity;
			$this->em->persist($this->entity);
		}

		$this->em->flush();
		$this->em->detach($this->entity);

		return $return;
	}

	/**
	 * @return array|null
	 */
	public function updateProjectsPage(int $page = 1, ?string $date = null)
	{
		$response = $this->connector->getProjects($page, $date);
		if (null === $response || !$response->isSuccessfull() || $page > $response->getTotalPages()) {
			return null;
		}

		if (null === $this->pages) {
			$this->pages = $response->getTotalPages();
		}

		$processedEntities = [];

		if (empty($response->getProjects())) {
			$msg = 'API response from XTM was invalid';
			$this->loggerSrv->addError($msg);
			throw new \UnexpectedValueException($msg);
		}

		foreach ($response->getProjects() as $item) {
			$this->projectData = $item;
			$action = $this->updateAnalyticsProject();

			$this->em->flush();
			$this->em->clear();

			foreach ($action as $lang => $result) {
				$status = Helper::resultToString($result['status']);
				$this->loggerSrv->addInfo('Analytics Project '.$item['name'].'('.$lang.'): '.$status.(isset($result['message']) ? ' '.$result['message'] : ''));
			}

			$processedEntities[] = [strval($item['id']), $action];
		}

		return $processedEntities;
	}

	public function getPages(): ?int
	{
		return $this->pages;
	}

	private function findLanguageTag($language): ?XtrfLanguage
	{
		$language = $this->languageHelper->translateLanguageCode($language);
		$lang = explode('-', $language);

		if (2 != strlen($lang[0])) {
			$this->loggerSrv->addNotice('Non-standard language code: '.$language);
			$languageTagEntity = $this->tagRepository->findByExternalIso1($language);
			if (null === $languageTagEntity) {
				$languageTagEntity = $this->tagRepository->findByExternalIso2($language);
			}
			if (null === $languageTagEntity) {
				$languageTagEntity = $this->tagRepository->findByExternalIso1(str_replace('-', '_', $language));
			}
			if (null === $languageTagEntity) {
				$languageTagEntity = $this->tagRepository->findByExternalIso2(str_replace('-', '_', $language));
			}
			if (null === $languageTagEntity) {
				$languageTagEntity = $this->tagRepository->findByExternalIso1($lang[0]);
			}
			if (null === $languageTagEntity) {
				$languageTagEntity = $this->tagRepository->findByExternalIso2($lang[0]);
			}

			if ($languageTagEntity) {
				return $languageTagEntity;
			}

			$this->loggerSrv->addInfo('Language not found by ExternalIso1 or ExternalIso2 column');

			return null;
		}

		if (isset($lang[2])) {
			$this->loggerSrv->addInfo('Language code contains script code');
			$languageTagEntity =
				$this->tagRepository->findByLanguageCountryScript(strtolower($lang[0]), strtoupper($lang[1]), $lang[2]);
		} elseif (isset($lang[1])) {
			$this->loggerSrv->addInfo('Language code contains country code');
			$languageTagEntity =
				$this->tagRepository->findByLanguageCountryScript(strtolower($lang[0]), strtoupper($lang[1]), null);
		} else {
			$this->loggerSrv->addInfo('Language code contains only language code');
			$languageTagEntity = $this->tagRepository->findByLanguageCountryScript(strtolower($lang[0]), null, null);
		}

		if (empty($languageTagEntity)) {
			$this->loggerSrv->addNotice('Matching language not found. Removing last element and looking again');
			$langTmp = $lang;
			array_pop($langTmp);
			if (isset($langTmp[1])) {
				$languageTagEntity =
					$this->tagRepository->findByLanguageCountryScript(strtolower($lang[0]), strtoupper($lang[1]), null);
			} else {
				$languageTagEntity = $this->tagRepository->findByLanguageCountry(strtolower($lang[0]), null);
			}
		}

		if (empty($languageTagEntity)) {
			$this->loggerSrv->addNotice('Matching language not found. Falling back to ExternalIso1 check');
			$languageTagEntity = $this->tagRepository->findByExternalIso1($language);
			if (empty($languageTagEntity)) {
				$languageTagEntity = $this->tagRepository->findByExternalIso1(str_replace('-', '_', $language));
			}
		} elseif (is_array($languageTagEntity) && count($languageTagEntity) > 1) {
			$this->loggerSrv->addNotice('There is more than one entity of '.$language.
				' language in database! Falling back to ExternalIso1 check');
			$languageTagEntity = $this->tagRepository->findByExternalIso1($language);
			if (empty($languageTagEntity)) {
				$languageTagEntity = $this->tagRepository->findByExternalIso1(str_replace('-', '_', $language));
			}
		}

		if (empty($languageTagEntity)) {
			return null;
		} elseif (is_array($languageTagEntity) && count($languageTagEntity) > 1) {
			$message = 'There is more than one entity of '.$language.' language in database!';
			$this->loggerSrv->addError($message);
			throw new \UnexpectedValueException($message);
		}

		if (is_array($languageTagEntity)) {
			$languageTagEntity = $languageTagEntity[0];
		}

		return $languageTagEntity;
	}

	/**
	 * @return null
	 */
	private function findProject($analyticsProjectId, $languageCode)
	{
		$this->entity = $this->repository->findByLanguageCode($analyticsProjectId, $languageCode);
	}
}
