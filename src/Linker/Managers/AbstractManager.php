<?php

namespace App\Linker\Managers;

use App\Command\Services\Helper;
use App\Service\LoggerService;
use App\Model\Entity\Parameter;
use App\Model\Entity\FetchQueue;
use App\Model\Utils\ParameterHelper;
use App\Model\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Command\Services\AlertBuilderService;
use App\Model\Repository\FetchQueueRepository;

abstract class AbstractManager
{
	protected $entity;

	protected $entityName;

	protected $entitySource;

	protected $entityCreated = false;

	protected $queueUsed = true;

	protected $alertBuilder;

	protected $parameterHelper;
	/**
	 * @var array
	 */
	protected $queueIds = [];
	/**
	 * @var Parameter
	 */
	protected $lastUpdate;
	/**
	 * @var FetchQueueRepository
	 */
	private $repository;
	/**
	 * @var LoggerService
	 */
	private $loggerSrv;
	/**
	 * @var EntityManagerInterface
	 */
	private $em;
	/**
	 * @var ManagerRegistry
	 */
	private $managerRegistry;

	public function __construct(
		EntityManagerInterface $em,
		ManagerRegistry $managerRegistry,
		LoggerService $loggerService,
		ParameterHelper $parameterHelper,
		AlertBuilderService $alertBuilder,
		FetchQueueRepository $repository
	) {
		if (empty($this->entityName)) {
			$this->entityName = substr(substr(get_class($this), 16), 0, -7);
		}
		$this->em = $em;
		$this->managerRegistry = $managerRegistry;
		$this->repository = $repository;
		$this->alertBuilder = $alertBuilder;
		$this->loggerSrv = $loggerService;
		$this->parameterHelper = $parameterHelper;
	}

	public function getIdsForUpdate(int $limit = null): array
	{
		$this->queueUsed = true;
		$toProcess = $this->repository->findToProcess($this->entityName, $this->entitySource, $limit);
		$result = [];
		foreach ($toProcess as $val) {
			$result[] = $val['externalId'];
		}

		return $result;
	}

	public function removeFromQueue($externalId, bool $flush = true): void
	{
		$entity = $this->repository->findByExternalId($this->entityName, $externalId);
		if ($entity) {
			$this->em->remove($entity);
			if ($flush) {
				$this->em->flush();
			}
		}
	}

	/**
	 * @param bool $autoFlush
	 */
	protected function addIdsToQueue(array $list, $autoFlush = false, \DateTime $queueTime = null, string $entityName = null): void
	{
		$now = new \DateTime('-3 mins');
		if (null === $queueTime) {
			$queueTime = $now;
		}

		if (null === $entityName) {
			$setLastUpdate = true;
			$entityName = $this->entityName;
		} else {
			$setLastUpdate = false;
		}

		$entryCounter = 0;

		foreach ($list as $updateId) {
			$queueEntity = $this->repository->findOneBy([
				'externalId' => $updateId,
				'entity' => $entityName,
				'source' => $this->entitySource,
			]);

			if (null !== $queueEntity) {
				$entryTime = $queueEntity->getDate();
				if ($entryTime <= $queueTime) {
					continue;
				}
				// because doctrine cache...
			} elseif ($this->isInQueue($entityName, $updateId)) {
				continue;
			} else {
				$queueEntity = new FetchQueue();
				$this->setIsInQueue($entityName, $updateId);
			}

			$queueEntity->setSource($this->entitySource)
				->setEntity($entityName)
				->setExternalId($updateId)
				->setDate($queueTime);

			$this->em->persist($queueEntity);
			++$entryCounter;
		}
		if ($autoFlush && $entryCounter > 200) {
			$this->em->flush();
			$entryCounter = 0;
		}

		if ($setLastUpdate) {
			$this->setLastUpdate($now, true);
		}
	}

	/**
	 * @return mixed
	 */
	protected function isQueueUsed(): bool
	{
		return $this->queueUsed;
	}

	protected function linkEntity(
		string $subEntityFieldName,
		string $persistentObjectName,
		string $externalId = null,
		bool $addMissingToQueue = false
	): EntityInterface {
		$getMethod = 'get'.ucfirst($subEntityFieldName);
		$setMethod = 'set'.ucfirst($subEntityFieldName);

		if (null === $externalId) {
			return $this->entity->$setMethod(null);
		}

		if (!$this->entityCreated) {
			/* @var EntityInterface|null $subEntity */
			$subEntity = $this->entity->$getMethod();
		} else {
			$subEntity = null;
		}
		if (null === $subEntity || $subEntity->getExternalId() != $externalId) {
			$linkedEntity = $this->getDoctrine()->getRepository($persistentObjectName)->findByExternalId($externalId);
			if (null === $linkedEntity) {
				if ($addMissingToQueue) {
					$this->addIdsToQueue([$externalId], true, new \DateTime('-1 day'), Helper::getClassName($persistentObjectName));
				}
				$this->loggerSrv->addError($persistentObjectName.' with External ID '.$externalId.
					' was not found in database and cannot be linked as '.$subEntityFieldName.' in '.
					$this->entityName.
					(method_exists($this->entity, 'getHumanId') ? ': '.$this->entity->getHumanId() : ''));
				throw new \InvalidArgumentException($persistentObjectName.' with External ID '.$externalId.' was not found in database and cannot be linked as '.$subEntityFieldName.' in '.$this->entityName.(method_exists($this->entity, 'getHumanId') ? ': '.$this->entity->getHumanId() : ''));
			}

			return $this->entity->$setMethod($linkedEntity);
		}

		return $this->entity;
	}

	/**
	 * @param int[] $externalIds
	 */
	protected function linkMultipleEntities(
		string $subEntityFieldName,
		string $persistentObjectName,
		array $externalIds,
		bool $addMissingToQueue = false
	): void {
		$getMethod = 'get'.ucfirst($subEntityFieldName);

		// remove old ones
		foreach ($this->entity->$getMethod() as $subEntity) {
			$externalIdKey = array_search($subEntity->getExternalId(), $externalIds);
			if (false === $externalIdKey) {
				$this->entity->$getMethod()->removeElement($subEntity);
			} else {
				unset($externalIds[$externalIdKey]);
			}
		}

		// add new ones
		$subEntitiesRepository = $this->getDoctrine()->getRepository($persistentObjectName);
		foreach ($externalIds as $externalId) {
			$newSubEntity = $subEntitiesRepository->findOneByExternalId($externalId);
			if (null === $newSubEntity) {
				if ($addMissingToQueue) {
					$this->addIdsToQueue([$externalId], true, new \DateTime('-1 day'), Helper::getClassName($persistentObjectName));
				}
				$this->loggerSrv->addWarning($persistentObjectName.' with External ID '.$externalId.
					' was not found in database and cannot be linked as '.$subEntityFieldName.' in '.
					$this->entityName.
					(method_exists($this->entity, 'getHumanId') ? ': '.$this->entity->getHumanId() : ''));
				throw new \InvalidArgumentException($persistentObjectName.' with External ID '.$externalId.' was not found in database and cannot be linked as '.$subEntityFieldName.' in '.$this->entityName.(method_exists($this->entity, 'getHumanId') ? ': '.$this->entity->getHumanId() : ''));
			}
			$this->entity->$getMethod()->add($newSubEntity);
		}
	}

	/**
	 * @throws \Exception
	 */
	protected function convertDates(array $dates): array
	{
		$return = [];
		foreach ($dates as $key => $val) {
			if (null === $val['time']) {
				$return[$key] = null;
			} else {
				$return[$key] = new \DateTime('@'.substr($val['time'], 0, -3));
			}
		}

		return $return;
	}

	/**
	 * @throws \Exception
	 */
	protected function convertTextDate(string $date): ?\DateTime
	{
		if (empty($date)) {
			return null;
		} else {
			return new \DateTime($date);
		}
	}

	protected function convertUnixDate(int $date): ?\DateTime
	{
		if (empty($date)) {
			return null;
		} else {
			$seconds = ceil($date / 1000);

			return new \DateTime("@$seconds");
		}
	}

	protected function convertToStringOrNull($string): ?string
	{
		if (empty($string)) {
			return null;
		} else {
			return strval($string);
		}
	}

	protected function checkEqualNumbers($num1, $num2, int $precision = 2): bool
	{
		if (null === $num1 && null === $num2) {
			return true;
		}
		if (null === $num1 || null === $num2) {
			return false;
		}
		$multiplier = pow(10, $precision);

		return intval($num1 * $multiplier) == intval($num2 * $multiplier);
	}

	/**
	 * @throws \Exception
	 */
	protected function getLastUpdate(): ?\DateTime
	{
		$this->lastUpdate = $this->parameterHelper->get('lastUpdate', $this->entitySource.$this->entityName);

		if (null !== $this->lastUpdate) {
			$lastUpdateEntity = new \DateTime('@'.$this->lastUpdate);
		} else {
			$lastUpdateEntity = null;
		}

		return $lastUpdateEntity;
	}

	protected function setLastUpdate(\DateTime $time, $flush = false): void
	{
		$this->parameterHelper->set('lastUpdate', $time->getTimestamp(), $this->entitySource.$this->entityName, $flush);
	}

	protected function getDoctrine(): ManagerRegistry
	{
		return $this->managerRegistry;
	}

	protected function isInQueue($entityName, $externalId): bool
	{
		if (isset($this->queueIds[$entityName][$externalId])) {
			return true;
		}

		return false;
	}

	protected function setIsInQueue($entityName, $externalId): void
	{
		$this->queueIds[$entityName][$externalId] = true;
	}
}
