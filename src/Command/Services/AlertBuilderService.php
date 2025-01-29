<?php

namespace App\Command\Services;

use App\Model\Entity\Alert;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\AlertRepository;

class AlertBuilderService
{
	private ?Alert $alert = null;
	private EntityManagerInterface $em;
	private AlertRepository $repository;

	public function __construct(EntityManagerInterface $entityManager, AlertRepository $repository)
	{
		$this->em = $entityManager;
		$this->repository = $repository;
	}

	public function create(): self
	{
		$this->alert = new Alert();
		$this->alert
			->setType(Alert::T_ACTION_NEEDED)
			->setTime(new \DateTime());

		return $this;
	}

	/**
	 * @throws \LogicException
	 */
	public function setEntity(object $entity): self
	{
		if (!$this->alert instanceof Alert) {
			throw new \LogicException('Alert object was not initialized. Use AlertBuilder::create() first');
		}

		if (method_exists($entity, 'getExternalId')) {
			$this->alert->setExternalId($entity->getExternalId());
		}
		$this->alert->setEntityId($entity->getId())
			->setEntityType(Helper::getClassName(get_class($entity)));

		return $this;
	}

	/**
	 * @throws \LogicException
	 */
	public function setType(int $type): self
	{
		if (!$this->alert instanceof Alert) {
			throw new \LogicException('Alert object was not initialized. Use AlertBuilder::create() first');
		}

		$this->alert->setType($type);

		return $this;
	}

	/**
	 * @throws \LogicException
	 */
	public function setDescription(string $description): self
	{
		if (!$this->alert instanceof Alert) {
			throw new \LogicException('Alert object was not initialized. Use AlertBuilder::create() first');
		}

		$this->alert->setDescription($description);

		return $this;
	}

	/**
	 * @return AlertBuilderService
	 *
	 * @throws \LogicException
	 */
	public function setAdditionalInfo(array $additionalInfo)
	{
		if (!$this->alert instanceof Alert) {
			throw new \LogicException('Alert object was not initialized. Use AlertBuilder::create() first');
		}

		ksort($additionalInfo);
		$date = date('Ymdhis');
		$additionalInfo = [$date => $additionalInfo];
		$this->alert->setAdditionalInfo($additionalInfo);

		return $this;
	}

	/**
	 * @throws \LogicException
	 */
	public function save(): self
	{
		if (!$this->alert instanceof Alert) {
			throw new \LogicException('Alert object was not initialized. Use AlertBuilder::create() first');
		}

		if (null !== $this->alert->getExternalId()) {
			$alerts = $this->repository->findByEntityTypeAndExternalId($this->alert->getEntityType(), $this->alert->getExternalId());
		} else {
			$alerts = $this->repository->findByEntityTypeAndId($this->alert->getEntityType(), $this->alert->getId());
		}
		if (null !== $alerts) {
			foreach ($alerts as $alert) {
				/* @var $alert Alert */
				if ($alert->getDescription() == $this->alert->getDescription()) {
					if (empty($alert->getAdditionalInfo())) {
						$this->alert = null;

						return $this;
					}
					$additionalInfos = $this->alert->getAdditionalInfo();
					$addI = $alert->getAdditionalInfo();
					$additionalInfoLocal = array_shift($addI);
					foreach ($additionalInfos as $additionalInfo) {
						// Magical way to compare 2 arrays. Assumption - they are both sorted by keys
						if (serialize($additionalInfo) === serialize($additionalInfoLocal)) {
							$this->alert = null;

							return $this;
						}
					}
					$additionalInfos = array_merge($additionalInfos, $additionalInfoLocal);
					$alert->setAdditionalInfo($additionalInfos);
					$this->em->persist($alert);
					$this->em->flush();
					$unitOfWork = $this->em->getUnitOfWork();
					$entities = $unitOfWork->getIdentityMap()[get_class($this->alert)] ?? [];
					foreach ($entities as $entity) {
						$this->em->detach($entity);
					}
					$this->alert = null;

					return $this;
				}
			}
		}

		$this->em->persist($this->alert);
		$this->em->flush();
		$this->em->detach($this->alert);
		$this->alert = null;

		return $this;
	}

	public function __destruct()
	{
		if ($this->alert instanceof Alert) {
			$this->save();
		}
	}
}
