<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_charge')]
#[ORM\Entity]
class CustomerCharge implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_charge_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_charge_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'due_date', type: 'date', nullable: false)]
	private ?\DateTimeInterface $dueDate;

	#[ORM\Column(name: 'irrecoverable', type: 'boolean', nullable: true)]
	private ?bool $irrecoverable;

	#[ORM\Column(name: 'percent_of_total', type: 'decimal', precision: 8, scale: 6, nullable: true)]
	private ?float $percentOfTotal;

	#[ORM\Column(name: 'value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $value;

	#[ORM\Column(name: 'prepayment_clearing_mode', type: 'string', nullable: true)]
	private ?string $prepaymentClearingMode;

	#[ORM\Column(name: 'charge_type_id', type: 'bigint', nullable: false)]
	private string $chargeTypeId;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\ManyToOne(targetEntity: CustomerInvoice::class, inversedBy: 'customerCharges')]
	#[ORM\JoinColumn(name: 'customer_invoice_id', referencedColumnName: 'customer_invoice_id', nullable: true)]
	private ?CustomerInvoice $customerInvoice;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', nullable: true)]
	private ?Project $project;

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

	public function getDueDate(): ?\DateTimeInterface
	{
		return $this->dueDate;
	}

	/**
	 * @return mixed
	 */
	public function setDueDate(\DateTimeInterface $dueDate): self
	{
		$this->dueDate = $dueDate;

		return $this;
	}

	public function getIrrecoverable(): ?bool
	{
		return $this->irrecoverable;
	}

	/**
	 * @return mixed
	 */
	public function setIrrecoverable(?bool $irrecoverable): self
	{
		$this->irrecoverable = $irrecoverable;

		return $this;
	}

	public function getPercentOfTotal(): ?string
	{
		return $this->percentOfTotal;
	}

	/**
	 * @return mixed
	 */
	public function setPercentOfTotal(?string $percentOfTotal): self
	{
		$this->percentOfTotal = $percentOfTotal;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function setValue(string $value): self
	{
		$this->value = $value;

		return $this;
	}

	public function getPrepaymentClearingMode(): ?string
	{
		return $this->prepaymentClearingMode;
	}

	/**
	 * @return mixed
	 */
	public function setPrepaymentClearingMode(?string $prepaymentClearingMode): self
	{
		$this->prepaymentClearingMode = $prepaymentClearingMode;

		return $this;
	}

	public function getChargeTypeId(): ?string
	{
		return $this->chargeTypeId;
	}

	/**
	 * @return mixed
	 */
	public function setChargeTypeId(string $chargeTypeId): self
	{
		$this->chargeTypeId = $chargeTypeId;

		return $this;
	}

	public function getCurrency(): ?Currency
	{
		return $this->currency;
	}

	/**
	 * @return mixed
	 */
	public function setCurrency(?Currency $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	/**
	 * @return mixed
	 */
	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getCustomerInvoice(): ?CustomerInvoice
	{
		return $this->customerInvoice;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerInvoice(?CustomerInvoice $customerInvoice): self
	{
		$this->customerInvoice = $customerInvoice;

		return $this;
	}

	public function getProject(): ?Project
	{
		return $this->project;
	}

	/**
	 * @return mixed
	 */
	public function setProject(?Project $project): self
	{
		$this->project = $project;

		return $this;
	}
}
