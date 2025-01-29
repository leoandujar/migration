<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table]
#[ORM\UniqueConstraint(name: 'entity_set_idx', columns: ['external_id', 'entity', 'source'])]
#[ORM\Entity]
class FetchQueue implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(type: 'guid')]
	private string $id;

	#[ORM\Column(type: 'datetime')]
	private ?\DateTimeInterface  $date;

	#[ORM\Column(type: 'string')]
	private string $source;

	#[ORM\Column(type: 'string')]
	private string $entity;

	#[ORM\Column(type: 'string')]
	private string $externalId;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getDate(): \DateTime
	{
		return $this->date;
	}

	public function setDate(\DateTime $date): FetchQueue
	{
		$this->date = $date;

		return $this;
	}

	public function getSource(): string
	{
		return $this->source;
	}

	public function setSource(string $source): FetchQueue
	{
		$this->source = $source;

		return $this;
	}

	public function getEntity(): string
	{
		return $this->entity;
	}

	public function setEntity(string $entity): FetchQueue
	{
		$this->entity = $entity;

		return $this;
	}

	public function getExternalId(): int
	{
		return $this->externalId;
	}

	public function setExternalId(int $externalId): FetchQueue
	{
		$this->externalId = $externalId;

		return $this;
	}
}
