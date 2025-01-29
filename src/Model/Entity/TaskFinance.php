<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'task_finance')]
#[ORM\Entity]
class TaskFinance implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'task_finance_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'task_finance_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'amount_modifiers', type: 'string', nullable: true)]
	private ?string $amountModifiers;

	#[ORM\Column(name: 'auto_total_agreed', type: 'boolean', nullable: true)]
	private ?bool $autoTotalAgreed;

	#[ORM\Column(name: 'backup_old_exchange_ratio_not_used', type: 'decimal', precision: 19, scale: 10, nullable: true)]
	private ?float $backupOldExchangeRatioNotUsed;

	#[ORM\Column(name: 'exchange_ratio_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $exchangeRatioDate;

	#[ORM\Column(name: 'exchange_ratio_event', type: 'string', nullable: true)]
	private ?string $exchangeRatioEvent;

	#[ORM\Column(name: 'ignore_minimal_charge', type: 'boolean', nullable: true)]
	private ?bool $ignoreMinimalCharge;

	#[ORM\Column(name: 'manual_amount_modifier_name', type: 'text', nullable: true)]
	private ?string $manualAmountModifierName;

	#[ORM\Column(name: 'minimal_charge', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $minimalCharge;

	#[ORM\Column(name: 'total_agreed', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $totalAgreed;

	#[ORM\Column(name: 'total_amount_modifier', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $totalAmountModifier;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'jobs')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', nullable: true)]
	private ?Task $task;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\OneToMany(targetEntity: TaskCatCharge::class, mappedBy: 'taskFinance', orphanRemoval: true)]
	private mixed $taskCatCharges;

	#[ORM\OneToMany(targetEntity: TaskCharge::class, mappedBy: 'taskFinance', orphanRemoval: true)]
	private mixed $taskCharges;

	#[ORM\OneToMany(targetEntity: TaskAmountModifier::class, mappedBy: 'taskFinance', orphanRemoval: true)]
	private mixed $amountModifiersList;

	#[ORM\Column(name: 'confirmation_sent_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $confirmationSentDate;

	public function __construct()
	{
		$this->taskCatCharges      = new ArrayCollection();
		$this->taskCharges         = new ArrayCollection();
		$this->amountModifiersList = new ArrayCollection();
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

	public function getAmountModifiers(): ?string
	{
		return $this->amountModifiers;
	}

	public function setAmountModifiers(?string $amountModifiers): self
	{
		$this->amountModifiers = $amountModifiers;

		return $this;
	}

	public function getAutoTotalAgreed(): ?bool
	{
		return $this->autoTotalAgreed;
	}

	public function setAutoTotalAgreed(?bool $autoTotalAgreed): self
	{
		$this->autoTotalAgreed = $autoTotalAgreed;

		return $this;
	}

	public function getBackupOldExchangeRatioNotUsed(): ?string
	{
		return $this->backupOldExchangeRatioNotUsed;
	}

	public function setBackupOldExchangeRatioNotUsed(?string $backupOldExchangeRatioNotUsed): self
	{
		$this->backupOldExchangeRatioNotUsed = $backupOldExchangeRatioNotUsed;

		return $this;
	}

	public function getExchangeRatioDate(): ?\DateTimeInterface
	{
		return $this->exchangeRatioDate;
	}

	public function setExchangeRatioDate(?\DateTimeInterface $exchangeRatioDate): self
	{
		$this->exchangeRatioDate = $exchangeRatioDate;

		return $this;
	}

	public function getExchangeRatioEvent(): ?string
	{
		return $this->exchangeRatioEvent;
	}

	public function setExchangeRatioEvent(?string $exchangeRatioEvent): self
	{
		$this->exchangeRatioEvent = $exchangeRatioEvent;

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

	public function getManualAmountModifierName(): ?string
	{
		return $this->manualAmountModifierName;
	}

	public function setManualAmountModifierName(?string $manualAmountModifierName): self
	{
		$this->manualAmountModifierName = $manualAmountModifierName;

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

	public function getTotalAgreed(): ?string
	{
		return $this->totalAgreed;
	}

	public function setTotalAgreed(string $totalAgreed): self
	{
		$this->totalAgreed = $totalAgreed;

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

	public function getConfirmationSentDate(): ?\DateTimeInterface
	{
		return $this->confirmationSentDate;
	}

	public function setConfirmationSentDate(?\DateTimeInterface $confirmationSentDate): self
	{
		$this->confirmationSentDate = $confirmationSentDate;

		return $this;
	}

	public function getTask(): ?Task
	{
		return $this->task;
	}

	public function setTask(?Task $task): self
	{
		$this->task = $task;

		return $this;
	}

	public function getCurrency(): ?Currency
	{
		return $this->currency;
	}

	public function setCurrency(?Currency $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getTaskCatCharges(): Collection
	{
		return $this->taskCatCharges;
	}

	public function addTaskCatCharge(TaskCatCharge $taskCatCharge): self
	{
		if (!$this->taskCatCharges->contains($taskCatCharge)) {
			$this->taskCatCharges[] = $taskCatCharge;
			$taskCatCharge->setTaskFinance($this);
		}

		return $this;
	}

	public function removeTaskCatCharge(TaskCatCharge $taskCatCharge): self
	{
		if ($this->taskCatCharges->contains($taskCatCharge)) {
			$this->taskCatCharges->removeElement($taskCatCharge);
			// set the owning side to null (unless already changed)
			if ($taskCatCharge->getTaskFinance() === $this) {
				$taskCatCharge->setTaskFinance(null);
			}
		}

		return $this;
	}

	public function getTaskCharges(): Collection
	{
		return $this->taskCharges;
	}

	public function addTaskCharge(TaskCharge $taskCharge): self
	{
		if (!$this->taskCharges->contains($taskCharge)) {
			$this->taskCharges[] = $taskCharge;
			$taskCharge->setTaskFinance($this);
		}

		return $this;
	}

	public function removeTaskCharge(TaskCharge $taskCharge): self
	{
		if ($this->taskCharges->contains($taskCharge)) {
			$this->taskCharges->removeElement($taskCharge);
			// set the owning side to null (unless already changed)
			if ($taskCharge->getTaskFinance() === $this) {
				$taskCharge->setTaskFinance(null);
			}
		}

		return $this;
	}

	public function getAmountModifiersList(): Collection
	{
		return $this->amountModifiersList;
	}

	public function addAmountModifiersList(TaskAmountModifier $amountModifiersList): self
	{
		if (!$this->amountModifiersList->contains($amountModifiersList)) {
			$this->amountModifiersList[] = $amountModifiersList;
			$amountModifiersList->setTaskFinance($this);
		}

		return $this;
	}

	public function removeAmountModifiersList(TaskAmountModifier $amountModifiersList): self
	{
		if ($this->amountModifiersList->contains($amountModifiersList)) {
			$this->amountModifiersList->removeElement($amountModifiersList);
			// set the owning side to null (unless already changed)
			if ($amountModifiersList->getTaskFinance() === $this) {
				$amountModifiersList->setTaskFinance(null);
			}
		}

		return $this;
	}
}
