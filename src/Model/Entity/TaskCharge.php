<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'task_charge')]
#[ORM\Entity]
class TaskCharge implements EntityInterface
{
	public const STATUS_ASSIGNED  = 'ASSIGNED';
	public const STATUS_OPEN      = 'OPEN';
	public const STATUS_CANCELLED = 'CANCELLED';
	public const STATUS_SENT      = 'SENT';

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
	#[SequenceGenerator(sequenceName: 'task_charge_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'task_charge_id', type: 'bigint')]
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

	#[ORM\Column(name: 'manual_amount_modifier_name', type: 'text', nullable: true)]
	private ?string $manualAmountModifierName;

	#[ORM\Column(name: 'order_confirmation_status', type: 'string', nullable: false)]
	private string $orderConfirmationStatus;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\ManyToOne(targetEntity: CalculationUnit::class)]
	#[ORM\JoinColumn(name: 'calculation_unit_id', referencedColumnName: 'calculation_unit_id', nullable: false)]
	private CalculationUnit $calculationUnit;

	#[ORM\ManyToOne(targetEntity: ActivityType::class)]
	#[ORM\JoinColumn(name: 'activity_type_id', referencedColumnName: 'activity_type_id', nullable: false)]
	private ActivityType $activityType;

	#[ORM\ManyToOne(targetEntity: TaskFinance::class, inversedBy: 'taskCharges')]
	#[ORM\JoinColumn(name: 'task_finance_id', referencedColumnName: 'task_finance_id', nullable: false)]
	private TaskFinance $taskFinance;

	#[ORM\Column(name: 'assisted_automated_receivable_id', type: 'text', nullable: true)]
	private ?string $assistedAutomatedReceivableId;

	#[ORM\Column(name: 'is_or_was_automated', type: 'boolean', nullable: true)]
	private ?bool $isOrWasAutomated;

	#[ORM\Column(name: 'pa_receivable_id', type: 'string', nullable: true)]
	private ?string $paReceivableId;

	#[ORM\Column(name: 'total_value', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $totalValue;

	#[ORM\Column(name: 'old_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $oldRate;

	#[ORM\Column(name: 'old_rate_origin', type: 'string', nullable: true)]
	private ?string $oldRateOrigin;

	#[ORM\Column(name: 'old_quantity', type: 'decimal', precision: 19, scale: 3, nullable: true)]
	private ?float $oldQuantity;

	#[ORM\OneToMany(targetEntity: TaskChargeAmountModifier::class, mappedBy: 'taskCharge', orphanRemoval: true)]
	private mixed $taskChargeAmountModifiers;

	public function __construct()
	{
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

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getChargePosition(): ?int
	{
		return $this->chargePosition;
	}

	public function setChargePosition(?int $chargePosition): self
	{
		$this->chargePosition = $chargePosition;

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

	public function getIgnoreMinimalCharge(): ?bool
	{
		return $this->ignoreMinimalCharge;
	}

	public function setIgnoreMinimalCharge(?bool $ignoreMinimalCharge): self
	{
		$this->ignoreMinimalCharge = $ignoreMinimalCharge;

		return $this;
	}

	public function getMinimalCharge(): ?string
	{
		return $this->minimalCharge;
	}

	public function setMinimalCharge(?string $minimalCharge): self
	{
		$this->minimalCharge = $minimalCharge;

		return $this;
	}

	public function getRate(): ?string
	{
		return $this->rate;
	}

	public function setRate(string $rate): self
	{
		$this->rate = $rate;

		return $this;
	}

	public function getRateOrigin(): ?string
	{
		return $this->rateOrigin;
	}

	public function setRateOrigin(string $rateOrigin): self
	{
		$this->rateOrigin = $rateOrigin;

		return $this;
	}

	public function getRateOriginDetails(): ?string
	{
		return $this->rateOriginDetails;
	}

	public function setRateOriginDetails(?string $rateOriginDetails): self
	{
		$this->rateOriginDetails = $rateOriginDetails;

		return $this;
	}

	public function getTotalAmountModifier(): ?string
	{
		return $this->totalAmountModifier;
	}

	public function setTotalAmountModifier(?string $totalAmountModifier): self
	{
		$this->totalAmountModifier = $totalAmountModifier;

		return $this;
	}

	public function getPercentageChargeType(): ?string
	{
		return $this->percentageChargeType;
	}

	public function setPercentageChargeType(?string $percentageChargeType): self
	{
		$this->percentageChargeType = $percentageChargeType;

		return $this;
	}

	public function getQuantity(): ?string
	{
		return $this->quantity;
	}

	public function setQuantity(string $quantity): self
	{
		$this->quantity = $quantity;

		return $this;
	}

	public function getManualAmountModifierName(): ?string
	{
		return $this->manualAmountModifierName;
	}

	public function setManualAmountModifierName(?string $manualAmountModifierName): self
	{
		$this->manualAmountModifierName = $manualAmountModifierName;

		return $this;
	}

	public function getOrderConfirmationStatus(): ?string
	{
		return $this->orderConfirmationStatus;
	}

	public function setOrderConfirmationStatus(string $orderConfirmationStatus): self
	{
		$this->orderConfirmationStatus = $orderConfirmationStatus;

		return $this;
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getAssistedAutomatedReceivableId(): ?string
	{
		return $this->assistedAutomatedReceivableId;
	}

	public function setAssistedAutomatedReceivableId(?string $assistedAutomatedReceivableId): self
	{
		$this->assistedAutomatedReceivableId = $assistedAutomatedReceivableId;

		return $this;
	}

	public function getIsOrWasAutomated(): ?bool
	{
		return $this->isOrWasAutomated;
	}

	public function setIsOrWasAutomated(?bool $isOrWasAutomated): self
	{
		$this->isOrWasAutomated = $isOrWasAutomated;

		return $this;
	}

	public function getPaReceivableId(): ?string
	{
		return $this->paReceivableId;
	}

	public function setPaReceivableId(?string $paReceivableId): self
	{
		$this->paReceivableId = $paReceivableId;

		return $this;
	}

	public function getTotalValue(): ?string
	{
		return $this->totalValue;
	}

	public function setTotalValue(?string $totalValue): self
	{
		$this->totalValue = $totalValue;

		return $this;
	}

	public function getCalculationUnit(): ?CalculationUnit
	{
		return $this->calculationUnit;
	}

	public function setCalculationUnit(?CalculationUnit $calculationUnit): self
	{
		$this->calculationUnit = $calculationUnit;

		return $this;
	}

	public function getActivityType(): ?ActivityType
	{
		return $this->activityType;
	}

	public function setActivityType(?ActivityType $activityType): self
	{
		$this->activityType = $activityType;

		return $this;
	}

	public function getTaskFinance(): ?TaskFinance
	{
		return $this->taskFinance;
	}

	public function setTaskFinance(?TaskFinance $taskFinance): self
	{
		$this->taskFinance = $taskFinance;

		return $this;
	}

	public function getTaskChargeAmountModifiers(): Collection
	{
		return $this->taskChargeAmountModifiers;
	}

	public function addTaskChargeAmountModifier(TaskChargeAmountModifier $taskChargeAmountModifier): self
	{
		if (!$this->taskChargeAmountModifiers->contains($taskChargeAmountModifier)) {
			$this->taskChargeAmountModifiers[] = $taskChargeAmountModifier;
			$taskChargeAmountModifier->setTaskCharge($this);
		}

		return $this;
	}

	public function removeTaskChargeAmountModifier(TaskChargeAmountModifier $taskChargeAmountModifier): self
	{
		if ($this->taskChargeAmountModifiers->removeElement($taskChargeAmountModifier)) {
			// set the owning side to null (unless already changed)
			if ($taskChargeAmountModifier->getTaskCharge() === $this) {
				$taskChargeAmountModifier->setTaskCharge(null);
			}
		}

		return $this;
	}

	public function getOldRate(): ?string
	{
		return $this->oldRate;
	}

	public function setOldRate(string $oldRate): self
	{
		$this->oldRate = $oldRate;

		return $this;
	}

	public function getOldRateOrigin(): ?string
	{
		return $this->oldRateOrigin;
	}

	public function setOldRateOrigin(string $oldRateOrigin): self
	{
		$this->oldRateOrigin = $oldRateOrigin;

		return $this;
	}

	public function getOldQuantity(): ?string
	{
		return $this->oldQuantity;
	}

	public function setOldQuantity(string $oldQuantity): self
	{
		$this->oldQuantity = $oldQuantity;

		return $this;
	}
}
