<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'activity_charge')]
#[ORM\Entity]
class ActivityCharge implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'activity_charge_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'activity_charge_id', type: 'bigint')]
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

	#[ORM\Column(name: 'percentage_charge_type', type: 'string', nullable: true)]
	private ?string $percentageChargeType;

	#[ORM\Column(name: 'quantity', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantity;

	#[ORM\Column(name: 'synchronize_with_worklog', type: 'boolean', nullable: false)]
	private bool $synchronizeWithWorklog;

	#[ORM\ManyToOne(targetEntity: CalculationUnit::class)]
	#[ORM\JoinColumn(name: 'calculation_unit_id', referencedColumnName: 'calculation_unit_id', nullable: false)]
	private CalculationUnit $calculationUnit;

	#[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'activityCharge')]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', nullable: false)]
	private Activity $activity;

	#[ORM\Column(name: 'worklog_autocreated_charge', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $worklogAutocreatedCharge;

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
	public function getPercentageChargeType(): ?string
	{
		return $this->percentageChargeType;
	}

	/**
	 * @return mixed
	 */
	public function setPercentageChargeType(?string $percentageChargeType): self
	{
		$this->percentageChargeType = $percentageChargeType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getQuantity(): ?string
	{
		return $this->quantity;
	}

	/**
	 * @return mixed
	 */
	public function setQuantity(string $quantity): self
	{
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSynchronizeWithWorklog(): ?bool
	{
		return $this->synchronizeWithWorklog;
	}

	/**
	 * @return mixed
	 */
	public function setSynchronizeWithWorklog(bool $synchronizeWithWorklog): self
	{
		$this->synchronizeWithWorklog = $synchronizeWithWorklog;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getWorklogAutocreatedCharge(): ?bool
	{
		return $this->worklogAutocreatedCharge;
	}

	/**
	 * @return mixed
	 */
	public function setWorklogAutocreatedCharge(bool $worklogAutocreatedCharge): self
	{
		$this->worklogAutocreatedCharge = $worklogAutocreatedCharge;

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
