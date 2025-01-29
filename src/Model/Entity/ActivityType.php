<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'activity_type')]
#[ORM\UniqueConstraint(name: 'activity_type_name_key', columns: ['name'])]
#[ORM\Entity]
class ActivityType implements EntityInterface
{
	public const TYPE_REVIEW = 9;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'activity_type_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'activity_type_id', type: 'bigint')]
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

	#[ORM\Column(name: 'files_needed', type: 'boolean', nullable: true)]
	private ?bool $filesNeeded;

	#[ORM\Column(name: 'provided_by_customer', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $providedByCustomer;

	#[ORM\Column(name: 'velocity', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $velocity;

	#[ORM\Column(name: 'relation_to_language', type: 'string', nullable: false)]
	private string $relationToLanguage;

	#[ORM\Column(name: 'velocity_calculation_unit_id', type: 'bigint', nullable: true)]
	private ?string $velocityCalculationUnitId;

	#[ORM\ManyToMany(targetEntity: CalculationUnit::class, mappedBy: 'activitiesType', cascade: ['persist'])]
	private mixed $calculationUnits;

	#[ORM\ManyToOne(targetEntity: QboItem::class, inversedBy: 'activitiesType')]
	#[ORM\JoinColumn(name: 'qbo_item_id', referencedColumnName: 'qbo_item_id', nullable: true)]
	private QboItem $qboItem;

	public function __construct()
	{
		$this->calculationUnits = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	/**
	 * @return mixed
	 */
	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	/**
	 * @return mixed
	 */
	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	public function getDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultEntity(bool $defaultEntity): self
	{
		$this->defaultEntity = $defaultEntity;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	/**
	 * @return mixed
	 */
	public function setPreferedEntity(bool $preferedEntity): self
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	public function getFilesNeeded(): ?bool
	{
		return $this->filesNeeded;
	}

	/**
	 * @return mixed
	 */
	public function setFilesNeeded(?bool $filesNeeded): self
	{
		$this->filesNeeded = $filesNeeded;

		return $this;
	}

	public function getProvidedByCustomer(): ?bool
	{
		return $this->providedByCustomer;
	}

	/**
	 * @return mixed
	 */
	public function setProvidedByCustomer(bool $providedByCustomer): self
	{
		$this->providedByCustomer = $providedByCustomer;

		return $this;
	}

	public function getVelocity(): ?string
	{
		return $this->velocity;
	}

	/**
	 * @return mixed
	 */
	public function setVelocity(?string $velocity): self
	{
		$this->velocity = $velocity;

		return $this;
	}

	public function getRelationToLanguage(): ?string
	{
		return $this->relationToLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setRelationToLanguage(string $relationToLanguage): self
	{
		$this->relationToLanguage = $relationToLanguage;

		return $this;
	}

	public function getVelocityCalculationUnitId(): ?string
	{
		return $this->velocityCalculationUnitId;
	}

	/**
	 * @return mixed
	 */
	public function setVelocityCalculationUnitId(?string $velocityCalculationUnitId): self
	{
		$this->velocityCalculationUnitId = $velocityCalculationUnitId;

		return $this;
	}

	public function getCalculationUnits(): Collection
	{
		return $this->calculationUnits;
	}

	/**
	 * @return mixed
	 */
	public function addCalculationUnit(CalculationUnit $calculationUnit): self
	{
		if (!$this->calculationUnits->contains($calculationUnit)) {
			$this->calculationUnits[] = $calculationUnit;
			$calculationUnit->addActivitiesType($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCalculationUnit(CalculationUnit $calculationUnit): self
	{
		if ($this->calculationUnits->contains($calculationUnit)) {
			$this->calculationUnits->removeElement($calculationUnit);
			$calculationUnit->removeActivitiesType($this);
		}

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	public function isDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	public function isPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	public function isFilesNeeded(): ?bool
	{
		return $this->filesNeeded;
	}

	public function isProvidedByCustomer(): ?bool
	{
		return $this->providedByCustomer;
	}

	public function getQboItem(): ?QboItem
	{
		return $this->qboItem;
	}

	public function setQboItem(?QboItem $qboItem): self
	{
		$this->qboItem = $qboItem;

		return $this;
	}
}
