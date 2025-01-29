<?php

namespace App\Model\Repository;

use App\Model\Entity\Alert;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class AlertRepository extends EntityRepository
{
	public function __construct(EntityManagerInterface $entityManager)
	{
		$class = $entityManager->getClassMetadata(Alert::class);
		parent::__construct($entityManager, $class);
	}

	public function findByType(int $type)
	{
		return $this->findBy(['type' => $type]);
	}

	public function findByEntityTypeAndId(string $entityType, ?string $entityId): ?array
	{
		return $this->findBy(['entityType' => $entityType, 'entityId' => $entityId]);
	}

	public function findByEntityTypeAndExternalId(string $entityType, ?string $externalId): ?array
	{
		return $this->findBy(['entityType' => $entityType, 'externalId' => $externalId]);
	}

	public function findForDisplay($page = 1, $limit = 100): mixed
	{
		return $this->findBy([], ['time' => 'DESC'], $limit, ($page - 1) * $limit);
	}
}
