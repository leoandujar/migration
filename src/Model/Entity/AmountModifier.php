<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'amount_modifier')]
#[ORM\UniqueConstraint(name: 'amount_modifier_name_key', columns: ['name'])]
#[ORM\Entity]
class AmountModifier implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'amount_modifier_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'amount_modifier_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'description', type: 'string', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'am_type', type: 'string', nullable: false)]
	private string $amType;

	#[ORM\Column(name: 'value', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $value;

	#[ORM\ManyToMany(targetEntity: ActivityAmountModifier::class, mappedBy: 'amountModifier', cascade: ['persist'])]
	private mixed $activitiesList;

	#[ORM\OneToMany(targetEntity: TaskAmountModifier::class, mappedBy: 'amountModifier', orphanRemoval: true)]
	private mixed $tasksList;

	#[ORM\OneToMany(targetEntity: TaskCatChargeAmountModifier::class, mappedBy: 'amountModifier', orphanRemoval: true)]
	private mixed $taskCatChargeAmountModifiers;

	#[ORM\OneToMany(targetEntity: TaskChargeAmountModifier::class, mappedBy: 'amountModifier', orphanRemoval: true)]
	private mixed $taskChargeAmountModifiers;

	public function __construct()
	{
		$this->activitiesList = new ArrayCollection();
		$this->tasksList = new ArrayCollection();
		$this->taskCatChargeAmountModifiers = new ArrayCollection();
		$this->taskChargeAmountModifiers = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	public function isDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	public function setDefaultEntity(bool $defaultEntity): self
	{
		$this->defaultEntity = $defaultEntity;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function isPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	public function setPreferedEntity(bool $preferedEntity): self
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getAmType(): ?string
	{
		return $this->amType;
	}

	public function setAmType(string $amType): self
	{
		$this->amType = $amType;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function setValue(string $value): self
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * @return Collection<int, ActivityAmountModifier>
	 */
	public function getActivitiesList(): Collection
	{
		return $this->activitiesList;
	}

	public function addActivitiesList(ActivityAmountModifier $activitiesList): self
	{
		if (!$this->activitiesList->contains($activitiesList)) {
			$this->activitiesList[] = $activitiesList;
			$activitiesList->addAmountModifier($this);
		}

		return $this;
	}

	public function removeActivitiesList(ActivityAmountModifier $activitiesList): self
	{
		if ($this->activitiesList->removeElement($activitiesList)) {
			$activitiesList->removeAmountModifier($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, TaskAmountModifier>
	 */
	public function getTasksList(): Collection
	{
		return $this->tasksList;
	}

	public function addTasksList(TaskAmountModifier $tasksList): self
	{
		if (!$this->tasksList->contains($tasksList)) {
			$this->tasksList[] = $tasksList;
			$tasksList->setAmountModifier($this);
		}

		return $this;
	}

	public function removeTasksList(TaskAmountModifier $tasksList): self
	{
		if ($this->tasksList->removeElement($tasksList)) {
			// set the owning side to null (unless already changed)
			if ($tasksList->getAmountModifier() === $this) {
				$tasksList->setAmountModifier(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, TaskCatChargeAmountModifier>
	 */
	public function getTaskCatChargeAmountModifiers(): Collection
	{
		return $this->taskCatChargeAmountModifiers;
	}

	public function addTaskCatChargeAmountModifier(TaskCatChargeAmountModifier $taskCatChargeAmountModifier): self
	{
		if (!$this->taskCatChargeAmountModifiers->contains($taskCatChargeAmountModifier)) {
			$this->taskCatChargeAmountModifiers[] = $taskCatChargeAmountModifier;
			$taskCatChargeAmountModifier->setAmountModifier($this);
		}

		return $this;
	}

	public function removeTaskCatChargeAmountModifier(TaskCatChargeAmountModifier $taskCatChargeAmountModifier): self
	{
		if ($this->taskCatChargeAmountModifiers->removeElement($taskCatChargeAmountModifier)) {
			// set the owning side to null (unless already changed)
			if ($taskCatChargeAmountModifier->getAmountModifier() === $this) {
				$taskCatChargeAmountModifier->setAmountModifier(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, TaskChargeAmountModifier>
	 */
	public function getTaskChargeAmountModifiers(): Collection
	{
		return $this->taskChargeAmountModifiers;
	}

	public function addTaskChargeAmountModifier(TaskChargeAmountModifier $taskChargeAmountModifier): self
	{
		if (!$this->taskChargeAmountModifiers->contains($taskChargeAmountModifier)) {
			$this->taskChargeAmountModifiers[] = $taskChargeAmountModifier;
			$taskChargeAmountModifier->setAmountModifier($this);
		}

		return $this;
	}

	public function removeTaskChargeAmountModifier(TaskChargeAmountModifier $taskChargeAmountModifier): self
	{
		if ($this->taskChargeAmountModifiers->removeElement($taskChargeAmountModifier)) {
			// set the owning side to null (unless already changed)
			if ($taskChargeAmountModifier->getAmountModifier() === $this) {
				$taskChargeAmountModifier->setAmountModifier(null);
			}
		}

		return $this;
	}
}
