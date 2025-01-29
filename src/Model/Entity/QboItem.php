<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'qbo_item')]
#[ORM\Index(name: '', columns: ['qbo_item_id'])]
#[ORM\Entity]
class QboItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'qbo_item_id', type: 'string', nullable: false)]
	private string $id;

	#[ORM\Column(name: 'fully_qualified_name', type: 'string', nullable: true)]
	private ?string $fullyQualifiedName;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'type', type: 'string', nullable: true)]
	private ?string $type;

	#[ORM\Column(name: 'metadata_create_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataCreateTime;

	#[ORM\Column(name: 'metadata_last_updated_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastUpdatedTime;

	#[ORM\OneToMany(targetEntity: ActivityType::class, mappedBy: 'qboItem')]
	private mixed $activitiesType;

	#[ORM\Column(name: 'sku', type: 'string', nullable: true)]
	private ?string $sku;

	public function __construct()
	{
		$this->activitiesType = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getFullyQualifiedName(): ?string
	{
		return $this->fullyQualifiedName;
	}

	public function setFullyQualifiedName(?string $fullyQualifiedName): self
	{
		$this->fullyQualifiedName = $fullyQualifiedName;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getMetadataCreateTime(): ?\DateTimeInterface
	{
		return $this->metadataCreateTime;
	}

	public function setMetadataCreateTime(?\DateTimeInterface $metadataCreateTime): self
	{
		$this->metadataCreateTime = $metadataCreateTime;

		return $this;
	}

	public function getMetadataLastUpdatedTime(): ?\DateTimeInterface
	{
		return $this->metadataLastUpdatedTime;
	}

	public function setMetadataLastUpdatedTime(?\DateTimeInterface $metadataLastUpdatedTime): self
	{
		$this->metadataLastUpdatedTime = $metadataLastUpdatedTime;

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	public function getActivitiesType(): Collection
	{
		return $this->activitiesType;
	}

	public function addActivitiesType(ActivityType $activitiesType): self
	{
		if (!$this->activitiesType->contains($activitiesType)) {
			$this->activitiesType[] = $activitiesType;
			$activitiesType->setQboItem($this);
		}

		return $this;
	}

	public function removeActivitiesType(ActivityType $activitiesType): self
	{
		if ($this->activitiesType->removeElement($activitiesType)) {
			// set the owning side to null (unless already changed)
			if ($activitiesType->getQboItem() === $this) {
				$activitiesType->setQboItem(null);
			}
		}

		return $this;
	}

	public function getSku(): ?string
	{
		return $this->sku;
	}

	public function setSku(?string $sku): static
	{
		$this->sku = $sku;

		return $this;
	}
}
