<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'history_entry')]
#[ORM\Entity]
class HistoryEntry implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'history_entry_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'history_entry_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'responsible_compound_id', type: 'string', nullable: true)]
	private string $responsibleCompoundId;

	#[ORM\Column(name: 'responsible_name', type: 'string', nullable: true)]
	private string $responsibleName;

	#[ORM\Column(name: 'timestamp', type: 'integer', nullable: false)]
	private int $timestamp;

	#[ORM\Column(name: 'entities', type: 'json', nullable: true)]
	private ?array $entities;

	#[ORM\Column(name: 'actions', type: 'json', nullable: true)]
	private ?array $actions;

	#[ORM\Column(name: 'regions', type: 'json', nullable: true)]
	private ?array $regions;

	#[ORM\Column(name: 'initial', type: 'boolean', nullable: false)]
	private bool $initial;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getResponsibleCompoundId(): ?string
	{
		return $this->responsibleCompoundId;
	}

	public function setResponsibleCompoundId(?string $responsibleCompoundId): self
	{
		$this->responsibleCompoundId = $responsibleCompoundId;

		return $this;
	}

	public function getResponsibleName(): ?string
	{
		return $this->responsibleName;
	}

	public function setResponsibleName(?string $responsibleName): self
	{
		$this->responsibleName = $responsibleName;

		return $this;
	}

	public function getTimestamp(): ?int
	{
		return $this->timestamp;
	}

	public function setTimestamp(int $timestamp): self
	{
		$this->timestamp = $timestamp;

		return $this;
	}

	public function getEntities(): ?array
	{
		return $this->entities;
	}

	public function setEntities(?array $entities): self
	{
		$this->entities = $entities;

		return $this;
	}

	public function getActions(): ?array
	{
		return $this->actions;
	}

	public function setActions(?array $actions): self
	{
		$this->actions = $actions;

		return $this;
	}

	public function getRegions(): ?array
	{
		return $this->regions;
	}

	public function setRegions(?array $regions): self
	{
		$this->regions = $regions;

		return $this;
	}

	public function getInitial(): ?bool
	{
		return $this->initial;
	}

	public function setInitial(bool $initial): self
	{
		$this->initial = $initial;

		return $this;
	}
}
