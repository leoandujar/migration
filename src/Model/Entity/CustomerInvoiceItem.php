<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_invoice_item')]
#[ORM\Entity]
class CustomerInvoiceItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_invoice_item_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_invoice_item_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'amount_modifier', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $amountModifier;

	#[ORM\Column(name: 'item_position', type: 'integer', nullable: true)]
	private ?int $itemPosition;

	#[ORM\Column(name: 'line_netto', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $lineNetto;

	#[ORM\Column(name: 'name', type: 'text', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'quantity', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantity;

	#[ORM\Column(name: 'rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $rate;

	#[ORM\Column(name: 'unit', type: 'text', nullable: true)]
	private ?string $unit;

	#[ORM\Column(name: 'vat_name', type: 'string', nullable: true)]
	private ?string $vatName;

	#[ORM\Column(name: 'vat_rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $vatRate;

	#[ORM\ManyToOne(targetEntity: CustomerInvoice::class, inversedBy: 'customInvoiceItems')]
	#[ORM\JoinColumn(name: 'customer_invoice_id', referencedColumnName: 'customer_invoice_id', nullable: true)]
	private ?CustomerInvoice $customerInvoice;

	#[ORM\ManyToOne(targetEntity: CustomerCharge::class)]
	#[ORM\JoinColumn(name: 'customer_charge_id', referencedColumnName: 'customer_charge_id', nullable: true)]
	private ?CustomerCharge $customerCharge;

	#[ORM\ManyToOne(targetEntity: VatRate::class)]
	#[ORM\JoinColumn(name: 'vat_id', referencedColumnName: 'vat_rate_id', nullable: false)]
	private VatRate $vat;

	#[ORM\Column(name: 'original_item_id', type: 'integer', nullable: true)]
	private ?int $originalItemId;

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

	public function getAmountModifier(): ?string
	{
		return $this->amountModifier;
	}

	/**
	 * @return mixed
	 */
	public function setAmountModifier(string $amountModifier): self
	{
		$this->amountModifier = $amountModifier;

		return $this;
	}

	public function getItemPosition(): ?int
	{
		return $this->itemPosition;
	}

	/**
	 * @return mixed
	 */
	public function setItemPosition(?int $itemPosition): self
	{
		$this->itemPosition = $itemPosition;

		return $this;
	}

	public function getLineNetto(): ?string
	{
		return $this->lineNetto;
	}

	/**
	 * @return mixed
	 */
	public function setLineNetto(string $lineNetto): self
	{
		$this->lineNetto = $lineNetto;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

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

	public function getUnit(): ?string
	{
		return $this->unit;
	}

	/**
	 * @return mixed
	 */
	public function setUnit(?string $unit): self
	{
		$this->unit = $unit;

		return $this;
	}

	public function getVatName(): ?string
	{
		return $this->vatName;
	}

	/**
	 * @return mixed
	 */
	public function setVatName(?string $vatName): self
	{
		$this->vatName = $vatName;

		return $this;
	}

	public function getVatRate(): ?string
	{
		return $this->vatRate;
	}

	/**
	 * @return mixed
	 */
	public function setVatRate(string $vatRate): self
	{
		$this->vatRate = $vatRate;

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

	public function getCustomerCharge(): ?CustomerCharge
	{
		return $this->customerCharge;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerCharge(?CustomerCharge $customerCharge): self
	{
		$this->customerCharge = $customerCharge;

		return $this;
	}

	public function getVat(): ?VatRate
	{
		return $this->vat;
	}

	/**
	 * @return mixed
	 */
	public function setVat(?VatRate $vat): self
	{
		$this->vat = $vat;

		return $this;
	}

	public function getOriginalItemId(): ?int
	{
		return $this->originalItemId;
	}

	public function setOriginalItemId(?int $originalItemId): self
	{
		$this->originalItemId = $originalItemId;

		return $this;
	}
}
