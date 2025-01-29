<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'alert')]
#[ORM\Entity]
class Alert implements EntityInterface
{
	public const T_ACTION_NEEDED = 1;
	public const T_ATTENTION_NEEDED = 2;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'alert_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'alert_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $time;

	#[ORM\Column(type: 'string')]
	private string $entityType;

	#[ORM\Column(type: 'string', length: 36, nullable: true)]
	private ?string $entityId;

	#[ORM\Column(type: 'integer', nullable: true)]
	private ?int $externalId;

	#[ORM\Column(type: 'smallint')]
	private int $type;

	#[ORM\Column(type: 'text')]
	private string $description;

	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $additionalInfo;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getTime(): ?\DateTimeInterface
	{
		return $this->time;
	}

	/**
	 * @return mixed
	 */
	public function setTime(\DateTimeInterface $time): self
	{
		$this->time = $time;

		return $this;
	}

	public function getEntityType(): ?string
	{
		return $this->entityType;
	}

	/**
	 * @return mixed
	 */
	public function setEntityType(string $entityType): self
	{
		$this->entityType = $entityType;

		return $this;
	}

	public function getEntityId(): ?string
	{
		return $this->entityId;
	}

	/**
	 * @return mixed
	 */
	public function setEntityId(string $entityId): self
	{
		$this->entityId = $entityId;

		return $this;
	}

	public function getExternalId(): ?int
	{
		return $this->externalId;
	}

	/**
	 * @return mixed
	 */
	public function setExternalId(?int $externalId): self
	{
		$this->externalId = $externalId;

		return $this;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(int $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAdditionalInfo(): array
	{
		return $this->additionalInfo ?? [];
	}

	/**
	 * @return mixed
	 */
	public function setAdditionalInfo(array $additionalInfo): self
	{
		$this->additionalInfo = $additionalInfo;

		return $this;
	}
}
