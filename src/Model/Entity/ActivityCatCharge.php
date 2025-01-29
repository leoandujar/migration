<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'activity_cat_charge')]
#[ORM\Entity]
class ActivityCatCharge implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'activity_cat_charge_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'charge_position', type: 'integer', nullable: true)]
	private ?int $chargePosition;

	#[ORM\Column(name: 'description', type: 'text', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'ignore_minimal_charge', type: 'boolean', nullable: true)]
	private ?bool $ignoreMinimalCharge;

	#[ORM\Column(name: 'minimal_charge', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $minimalCharge;

	#[ORM\Column(name: 'rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $rate;

	#[ORM\Column(name: 'rate_origin', type: 'string', nullable: false)]
	private string $rateOrigin;

	#[ORM\Column(name: 'rate_origin_details', type: 'string', nullable: true)]
	private ?string $rateOriginDetails;

	#[ORM\Column(name: 'total_amount_modifier', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $totalAmountModifier;

	#[ORM\Column(name: 'calculated_in_external_system', type: 'boolean', nullable: true)]
	private ?bool $calculatedInExternalSystem;

	#[ORM\Column(name: 'cat_grid_no_match', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridNoMatch;

	#[ORM\Column(name: 'cat_grid_percent100', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridPercent100;

	#[ORM\Column(name: 'cat_grid_percent50_74', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridPercent5074;

	#[ORM\Column(name: 'cat_grid_percent75_84', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridPercent7584;

	#[ORM\Column(name: 'cat_grid_percent85_94', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridPercent8594;

	#[ORM\Column(name: 'cat_grid_percent95_99', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridPercent9599;

	#[ORM\Column(name: 'cat_grid_repetitions', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridRepetitions;

	#[ORM\Column(name: 'cat_grid_x_translated', type: 'decimal', precision: 19, scale: 4, nullable: false)]
	private float $gridXTranslated;

	#[ORM\Column(name: 'cat_quantity_no_match', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityNoMatch;

	#[ORM\Column(name: 'cat_quantity_percent100', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityPercent100;

	#[ORM\Column(name: 'cat_quantity_percent50_74', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityPercent5074;

	#[ORM\Column(name: 'cat_quantity_percent75_84', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityPercent7584;

	#[ORM\Column(name: 'cat_quantity_percent85_94', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityPercent8594;

	#[ORM\Column(name: 'cat_quantity_percent95_99', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityPercent9599;

	#[ORM\Column(name: 'cat_quantity_repetitions', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityRepetitions;

	#[ORM\Column(name: 'cat_quantity_x_translated', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantityXTranslated;

	#[ORM\Column(name: 'cat_grid_percent100_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridPercent100Rate;

	#[ORM\Column(name: 'cat_grid_percent50_74_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridPercent5074Rate;

	#[ORM\Column(name: 'cat_grid_percent75_84_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridPercent7584Rate;

	#[ORM\Column(name: 'cat_grid_percent85_94_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridPercent8594Rate;

	#[ORM\Column(name: 'cat_grid_percent95_99_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridPercent9599Rate;

	#[ORM\Column(name: 'cat_grid_repetitions_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridRepetitionsRate;

	#[ORM\Column(name: 'cat_grid_x_translated_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $gridXTranslatedRate;

	#[ORM\Column(name: 'fixed_rate_cat_grid_available', type: 'boolean', nullable: true)]
	private ?bool $fixedRateCatGridAvailable;

	#[ORM\Column(name: 'metrics_retrieved_from_external_system', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $metricsRetrievedFromExternalSystem;

	#[ORM\ManyToOne(targetEntity: CalculationUnit::class)]
	#[ORM\JoinColumn(name: 'calculation_unit_id', referencedColumnName: 'calculation_unit_id', nullable: false)]
	private CalculationUnit $calculationUnit;

	#[ORM\ManyToOne(targetEntity: TmSaving::class, inversedBy: 'activityCatCharge')]
	#[ORM\JoinColumn(name: 'tm_savings_id', referencedColumnName: 'tm_savings_id', nullable: false)]
	private TmSaving $tmSavings;

	#[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'activityCatCharge')]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', nullable: false)]
	private Activity $activity;

	#[ORM\Column(name: 'assisted_automated_payable_id', type: 'text', nullable: true)]
	private ?string $assistedAutomatedPayableId;

	#[ORM\Column(name: 'pa_payable_id', type: 'string', nullable: true)]
	private ?string $paPayableId;

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
	public function getChargePosition(): ?int
	{
		return $this->chargePosition;
	}

	/**
	 * @return mixed
	 */
	public function setChargePosition(?int $chargePosition): self
	{
		$this->chargePosition = $chargePosition;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIgnoreMinimalCharge(): ?bool
	{
		return $this->ignoreMinimalCharge;
	}

	/**
	 * @return mixed
	 */
	public function setIgnoreMinimalCharge(?bool $ignoreMinimalCharge): self
	{
		$this->ignoreMinimalCharge = $ignoreMinimalCharge;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMinimalCharge(): ?string
	{
		return $this->minimalCharge;
	}

	/**
	 * @return mixed
	 */
	public function setMinimalCharge(?string $minimalCharge): self
	{
		$this->minimalCharge = $minimalCharge;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRate(): ?string
	{
		return $this->rate;
	}

	/**
	 * @return mixed
	 */
	public function setRate(string $rate): self
	{
		$this->rate = $rate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRateOrigin(): ?string
	{
		return $this->rateOrigin;
	}

	/**
	 * @return mixed
	 */
	public function setRateOrigin(string $rateOrigin): self
	{
		$this->rateOrigin = $rateOrigin;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRateOriginDetails(): ?string
	{
		return $this->rateOriginDetails;
	}

	/**
	 * @return mixed
	 */
	public function setRateOriginDetails(?string $rateOriginDetails): self
	{
		$this->rateOriginDetails = $rateOriginDetails;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTotalAmountModifier(): ?string
	{
		return $this->totalAmountModifier;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAmountModifier(?string $totalAmountModifier): self
	{
		$this->totalAmountModifier = $totalAmountModifier;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCalculatedInExternalSystem(): ?bool
	{
		return $this->calculatedInExternalSystem;
	}

	/**
	 * @return mixed
	 */
	public function setCalculatedInExternalSystem(?bool $calculatedInExternalSystem): self
	{
		$this->calculatedInExternalSystem = $calculatedInExternalSystem;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridNoMatch(): ?string
	{
		return $this->gridNoMatch;
	}

	/**
	 * @return mixed
	 */
	public function setGridNoMatch(string $gridNoMatch): self
	{
		$this->gridNoMatch = $gridNoMatch;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent100(): ?string
	{
		return $this->gridPercent100;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent100(string $gridPercent100): self
	{
		$this->gridPercent100 = $gridPercent100;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent5074(): ?string
	{
		return $this->gridPercent5074;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent5074(string $gridPercent5074): self
	{
		$this->gridPercent5074 = $gridPercent5074;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent7584(): ?string
	{
		return $this->gridPercent7584;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent7584(string $gridPercent7584): self
	{
		$this->gridPercent7584 = $gridPercent7584;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent8594(): ?string
	{
		return $this->gridPercent8594;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent8594(string $gridPercent8594): self
	{
		$this->gridPercent8594 = $gridPercent8594;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent9599(): ?string
	{
		return $this->gridPercent9599;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent9599(string $gridPercent9599): self
	{
		$this->gridPercent9599 = $gridPercent9599;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridRepetitions(): ?string
	{
		return $this->gridRepetitions;
	}

	/**
	 * @return mixed
	 */
	public function setGridRepetitions(string $gridRepetitions): self
	{
		$this->gridRepetitions = $gridRepetitions;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridXTranslated(): ?string
	{
		return $this->gridXTranslated;
	}

	/**
	 * @return mixed
	 */
	public function setGridXTranslated(string $gridXTranslated): self
	{
		$this->gridXTranslated = $gridXTranslated;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityNoMatch(): ?string
	{
		return $this->quantityNoMatch;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityNoMatch(string $quantityNoMatch): self
	{
		$this->quantityNoMatch = $quantityNoMatch;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityPercent100(): ?string
	{
		return $this->quantityPercent100;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityPercent100(string $quantityPercent100): self
	{
		$this->quantityPercent100 = $quantityPercent100;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityPercent5074(): ?string
	{
		return $this->quantityPercent5074;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityPercent5074(string $quantityPercent5074): self
	{
		$this->quantityPercent5074 = $quantityPercent5074;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityPercent7584(): ?string
	{
		return $this->quantityPercent7584;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityPercent7584(string $quantityPercent7584): self
	{
		$this->quantityPercent7584 = $quantityPercent7584;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityPercent8594(): ?string
	{
		return $this->quantityPercent8594;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityPercent8594(string $quantityPercent8594): self
	{
		$this->quantityPercent8594 = $quantityPercent8594;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityPercent9599(): ?string
	{
		return $this->quantityPercent9599;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityPercent9599(string $quantityPercent9599): self
	{
		$this->quantityPercent9599 = $quantityPercent9599;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityRepetitions(): ?string
	{
		return $this->quantityRepetitions;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityRepetitions(string $quantityRepetitions): self
	{
		$this->quantityRepetitions = $quantityRepetitions;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantityXTranslated(): ?string
	{
		return $this->quantityXTranslated;
	}

	/**
	 * @return mixed
	 */
	public function setQuantityXTranslated(string $quantityXTranslated): self
	{
		$this->quantityXTranslated = $quantityXTranslated;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent100Rate(): ?string
	{
		return $this->gridPercent100Rate;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent100Rate(?string $gridPercent100Rate): self
	{
		$this->gridPercent100Rate = $gridPercent100Rate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent5074Rate(): ?string
	{
		return $this->gridPercent5074Rate;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent5074Rate(?string $gridPercent5074Rate): self
	{
		$this->gridPercent5074Rate = $gridPercent5074Rate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent7584Rate(): ?string
	{
		return $this->gridPercent7584Rate;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent7584Rate(?string $gridPercent7584Rate): self
	{
		$this->gridPercent7584Rate = $gridPercent7584Rate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent8594Rate(): ?string
	{
		return $this->gridPercent8594Rate;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent8594Rate(?string $gridPercent8594Rate): self
	{
		$this->gridPercent8594Rate = $gridPercent8594Rate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridPercent9599Rate(): ?string
	{
		return $this->gridPercent9599Rate;
	}

	/**
	 * @return mixed
	 */
	public function setGridPercent9599Rate(?string $gridPercent9599Rate): self
	{
		$this->gridPercent9599Rate = $gridPercent9599Rate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridRepetitionsRate(): ?string
	{
		return $this->gridRepetitionsRate;
	}

	/**
	 * @return mixed
	 */
	public function setGridRepetitionsRate(?string $gridRepetitionsRate): self
	{
		$this->gridRepetitionsRate = $gridRepetitionsRate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getGridXTranslatedRate(): ?string
	{
		return $this->gridXTranslatedRate;
	}

	/**
	 * @return mixed
	 */
	public function setGridXTranslatedRate(?string $gridXTranslatedRate): self
	{
		$this->gridXTranslatedRate = $gridXTranslatedRate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFixedRateCatGridAvailable(): ?bool
	{
		return $this->fixedRateCatGridAvailable;
	}

	/**
	 * @return mixed
	 */
	public function setFixedRateCatGridAvailable(?bool $fixedRateCatGridAvailable): self
	{
		$this->fixedRateCatGridAvailable = $fixedRateCatGridAvailable;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMetricsRetrievedFromExternalSystem(): ?bool
	{
		return $this->metricsRetrievedFromExternalSystem;
	}

	/**
	 * @return mixed
	 */
	public function setMetricsRetrievedFromExternalSystem(bool $metricsRetrievedFromExternalSystem): self
	{
		$this->metricsRetrievedFromExternalSystem = $metricsRetrievedFromExternalSystem;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAssistedAutomatedPayableId(): ?string
	{
		return $this->assistedAutomatedPayableId;
	}

	/**
	 * @return mixed
	 */
	public function setAssistedAutomatedPayableId(?string $assistedAutomatedPayableId): self
	{
		$this->assistedAutomatedPayableId = $assistedAutomatedPayableId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPaPayableId(): ?string
	{
		return $this->paPayableId;
	}

	/**
	 * @return mixed
	 */
	public function setPaPayableId(?string $paPayableId): self
	{
		$this->paPayableId = $paPayableId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCalculationUnit(): ?CalculationUnit
	{
		return $this->calculationUnit;
	}

	/**
	 * @return mixed
	 */
	public function setCalculationUnit(?CalculationUnit $calculationUnit): self
	{
		$this->calculationUnit = $calculationUnit;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTmSavings(): ?TmSaving
	{
		return $this->tmSavings;
	}

	/**
	 * @return mixed
	 */
	public function setTmSavings(?TmSaving $tmSavings): self
	{
		$this->tmSavings = $tmSavings;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getActivity(): ?Activity
	{
		return $this->activity;
	}

	/**
	 * @return mixed
	 */
	public function setActivity(?Activity $activity): self
	{
		$this->activity = $activity;

		return $this;
	}
}
