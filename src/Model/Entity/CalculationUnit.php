<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'calculation_unit')]
#[ORM\UniqueConstraint(name: 'calculation_unit_name_key', columns: ['name'])]
#[ORM\UniqueConstraint(name: 'calculation_unit_symbol_key', columns: ['symbol'])]
#[ORM\Entity]
class CalculationUnit implements EntityInterface
{
	public const UNIT_CHARACTER      = 'character';
	public const UNIT_SOURCE_WORD    = 'source word';
	public const UNIT_TARGET_WORD    = 'target word';
	public const UNIT_PERCENTAGE     = 'Percentage';
	public const UNIT_CUARTILLAS     = 'Cuartillas';
	public const UNIT_HOURS          = 'Hours';
	public const UNIT_MINUTES        = 'Minutes';
	public const UNIT_TASK           = 'task';
	public const UNIT_MINIMUN_CHARGE = 'Minimum Charge';
	public const UNIT_ESTIMATED_HOUR = 'Estimated hour';
	public const UNIT_ITEMS          = 'Items';
	public const UNIT_PAGE           = 'Page';
	public const UNIT_DOCUMENT       = 'document';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'calculation_unit_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'calculation_unit_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $active;

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

	#[ORM\Column(name: 'conversion_expression', type: 'text', nullable: true)]
	private ?string $conversionExpression;

	#[ORM\Column(name: 'exchange_ratio', type: 'decimal', precision: 19, scale: 10, nullable: false)]
	private float $exchangeRatio;

	#[ORM\Column(name: 'file_stats_conversion_expression', type: 'text', nullable: true)]
	private ?string $fileStatsConversionExpression;

	#[ORM\Column(name: 'symbol', type: 'string', nullable: false)]
	private string $symbol;

	#[ORM\Column(name: 'time_conversion_expression', type: 'text', nullable: true)]
	private ?string $timeConversionExpression;

	#[ORM\Column(name: 'type', type: 'string', nullable: false)]
	private string $type;

	#[ORM\Column(name: 'use_in_cat_analysis', type: 'boolean', nullable: false)]
	private bool $useInCatAnalysis;

	#[ORM\JoinTable(name: 'activity_type_calculation_units')]
	#[ORM\JoinColumn(name: 'calculation_unit_id', referencedColumnName: 'calculation_unit_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'activity_type_id', referencedColumnName: 'activity_type_id')]
	#[ORM\ManyToMany(targetEntity: ActivityType::class, cascade: ['persist'], inversedBy: 'calculationUnits')]
	protected mixed $activitiesType;

	public function __construct()
	{
		$this->activitiesType = new ArrayCollection();
	}

	// ################################ NORMAL RELATION FIELDS START HERE################

	/**
	 * @return mixed
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getConversionExpression(): ?string
	{
		return $this->conversionExpression;
	}

	/**
	 * @return mixed
	 */
	public function setConversionExpression(?string $conversionExpression): self
	{
		$this->conversionExpression = $conversionExpression;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExchangeRatio(): ?string
	{
		return $this->exchangeRatio;
	}

	/**
	 * @return mixed
	 */
	public function setExchangeRatio(string $exchangeRatio): self
	{
		$this->exchangeRatio = $exchangeRatio;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFileStatsConversionExpression(): ?string
	{
		return $this->fileStatsConversionExpression;
	}

	/**
	 * @return mixed
	 */
	public function setFileStatsConversionExpression(?string $fileStatsConversionExpression): self
	{
		$this->fileStatsConversionExpression = $fileStatsConversionExpression;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSymbol(): ?string
	{
		return $this->symbol;
	}

	/**
	 * @return mixed
	 */
	public function setSymbol(string $symbol): self
	{
		$this->symbol = $symbol;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTimeConversionExpression(): ?string
	{
		return $this->timeConversionExpression;
	}

	/**
	 * @return mixed
	 */
	public function setTimeConversionExpression(?string $timeConversionExpression): self
	{
		$this->timeConversionExpression = $timeConversionExpression;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUseInCatAnalysis(): ?bool
	{
		return $this->useInCatAnalysis;
	}

	/**
	 * @return mixed
	 */
	public function setUseInCatAnalysis(bool $useInCatAnalysis): self
	{
		$this->useInCatAnalysis = $useInCatAnalysis;

		return $this;
	}

	/**
	 * @return Collection|ActivityType[]
	 */
	public function getActivitiesType(): Collection
	{
		return $this->activitiesType;
	}

	/**
	 * @return mixed
	 */
	public function addActivitiesType(ActivityType $activitiesType): self
	{
		if (!$this->activitiesType->contains($activitiesType)) {
			$this->activitiesType[] = $activitiesType;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeActivitiesType(ActivityType $activitiesType): self
	{
		$this->activitiesType->removeElement($activitiesType);

		return $this;
	}
}
