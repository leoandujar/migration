<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'qbo_customer_invoice_item')]
#[ORM\Index(name: '', columns: ['qbo_customer_invoice_item_id'])]
#[ORM\Entity]
class QboCustomerInvoiceItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'qbo_customer_invoice_item_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'qbo_customer_invoice_item_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'remote_id', type: 'string', nullable: true)]
	private ?string $remoteId;

	#[ORM\Column(name: 'type', type: 'string', nullable: true)]
	private ?string $detailType;

	#[ORM\Column(name: 'line_netto', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $amount;

	#[ORM\Column(name: 'line_num', type: 'integer', nullable: true)]
	private ?int $lineNum;

	#[ORM\Column(name: 'description', type: 'string', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'discount_rate', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $discountRate;

	#[ORM\Column(name: 'discount_amt', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $discountAmt;

	#[ORM\Column(name: 'qbo_item_id', type: 'string', nullable: true)]
	private ?string $itemRef;

	#[ORM\Column(name: 'unit_price', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $unitPrice;

	#[ORM\Column(name: 'quantity', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $qty;

	#[ORM\Column(name: 'qbo_account_id', type: 'string', nullable: true)]
	private ?string $itemAccountRef;

	#[ORM\ManyToOne(targetEntity: CustomerInvoice::class)]
	#[ORM\JoinColumn(name: 'customer_invoice_id', referencedColumnName: 'customer_invoice_id', nullable: false)]
	private CustomerInvoice $customerInvoice;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getDetailType(): ?string
	{
		return $this->detailType;
	}

	public function setDetailType(?string $detailType): self
	{
		$this->detailType = $detailType;

		return $this;
	}

	public function getAmount(): ?string
	{
		return $this->amount;
	}

	public function setAmount(?string $amount): self
	{
		$this->amount = $amount;

		return $this;
	}

	public function getLineNum(): ?int
	{
		return $this->lineNum;
	}

	public function setLineNum(?int $lineNum): self
	{
		$this->lineNum = $lineNum;

		return $this;
	}

	public function getQty(): ?string
	{
		return $this->qty;
	}

	public function setQty(?string $qty): self
	{
		$this->qty = $qty;

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

	public function getItemAccountRef(): ?string
	{
		return $this->itemAccountRef;
	}

	public function setItemAccountRef(?string $itemAccountRef): self
	{
		$this->itemAccountRef = $itemAccountRef;

		return $this;
	}

	public function getDiscountRate(): ?string
	{
		return $this->discountRate;
	}

	public function setDiscountRate(?string $discountRate): self
	{
		$this->discountRate = $discountRate;

		return $this;
	}

	public function getDiscountAmt(): ?string
	{
		return $this->discountAmt;
	}

	public function setDiscountAmt(?string $discountAmt): self
	{
		$this->discountAmt = $discountAmt;

		return $this;
	}

	public function getUnitPrice(): ?string
	{
		return $this->unitPrice;
	}

	public function setUnitPrice(?string $unitPrice): self
	{
		$this->unitPrice = $unitPrice;

		return $this;
	}

	public function getItemRef(): ?string
	{
		return $this->itemRef;
	}

	public function setItemRef(?string $itemRef): self
	{
		$this->itemRef = $itemRef;

		return $this;
	}

	public function getCustomerInvoice(): ?CustomerInvoice
	{
		return $this->customerInvoice;
	}

	public function setCustomerInvoice(?CustomerInvoice $customerInvoice): self
	{
		$this->customerInvoice = $customerInvoice;

		return $this;
	}

	public function getRemoteId(): ?string
	{
		return $this->remoteId;
	}

	public function setRemoteId(?string $remoteId): self
	{
		$this->remoteId = $remoteId;

		return $this;
	}
}
