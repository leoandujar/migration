<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'rejection_reason')]
#[ORM\UniqueConstraint(name: 'rejection_reason_name_unique', columns: ['name'])]
#[ORM\Entity]
class RejectionReason implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'rejection_reason_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'rejection_reason_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false)]
	private bool $active;

	#[ORM\Column(name: 'version', type: 'bigint', nullable: false)]
	private string $version;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'name', type: 'text', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'visible_to_customer', type: 'boolean', nullable: false)]
	private bool $visibleToCustomer;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): static
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(bool $active): static
	{
		$this->active = $active;

		return $this;
	}

	public function getVersion(): ?string
	{
		return $this->version;
	}

	public function setVersion(string $version): static
	{
		$this->version = $version;

		return $this;
	}

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	public function setLocalizedEntity(?array $localizedEntity): static
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function isPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	public function setPreferedEntity(bool $preferedEntity): static
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	public function isDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	public function setDefaultEntity(bool $defaultEntity): static
	{
		$this->defaultEntity = $defaultEntity;

		return $this;
	}

	public function isVisibleToCustomer(): ?bool
	{
		return $this->visibleToCustomer;
	}

	public function setVisibleToCustomer(bool $visibleToCustomer): static
	{
		$this->visibleToCustomer = $visibleToCustomer;

		return $this;
	}
}
