<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'qbo_payment_item')]
#[ORM\Index(name: '', columns: ['qbo_payment_item_id'])]
#[ORM\Entity]
class QboPaymentItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'qbo_payment_item_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'qbo_payment_item_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'remote_id', type: 'string', nullable: true)]
	private ?string $remoteId;

	#[ORM\Column(name: 'transaction_type', type: 'string', nullable: true)]
	private ?string $transactionType;

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
	private string $itemAccountRef;

	#[ORM\ManyToOne(targetEntity: QboCustomerPayment::class)]
	#[ORM\JoinColumn(name: 'qbo_customer_payment_id', referencedColumnName: 'qbo_customer_payment_id', nullable: true)]
	private ?QboCustomerPayment $qboCustomerPayment;

	#[ORM\ManyToOne(targetEntity: CustomerInvoice::class)]
	#[ORM\JoinColumn(name: 'xtrf_customer_invoice_id', referencedColumnName: 'customer_invoice_id', nullable: true)]
	private ?CustomerInvoice $xtrfCustomerInvoice;

	#[ORM\ManyToOne(targetEntity: QboProviderPayment::class)]
	#[ORM\JoinColumn(name: 'qbo_provider_payment_id', referencedColumnName: 'qbo_provider_payment_id', nullable: true)]
	private ?QboProviderPayment $qboProviderPayment;

	#[ORM\ManyToOne(targetEntity: QboProviderInvoice::class)]
	#[ORM\JoinColumn(name: 'qbo_provider_invoice_id', referencedColumnName: 'qbo_provider_invoice_id', nullable: true)]
	private ?QboProviderInvoice $qboProviderInvoice;

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

	public function getQboCustomerPayment(): ?QboCustomerPayment
	{
		return $this->qboCustomerPayment;
	}

	public function setQboCustomerPayment(?QboCustomerPayment $qboCustomerPayment): self
	{
		$this->qboCustomerPayment = $qboCustomerPayment;

		return $this;
	}

	public function getQboProviderPayment(): ?QboProviderPayment
	{
		return $this->qboProviderPayment;
	}

	public function setQboProviderPayment(?QboProviderPayment $qboProviderPayment): self
	{
		$this->qboProviderPayment = $qboProviderPayment;

		return $this;
	}

	public function getXtrfCustomerInvoice(): ?CustomerInvoice
	{
		return $this->xtrfCustomerInvoice;
	}

	public function setXtrfCustomerInvoice(?CustomerInvoice $xtrfCustomerInvoice): self
	{
		$this->xtrfCustomerInvoice = $xtrfCustomerInvoice;

		return $this;
	}

	public function getQboProviderInvoice(): ?QboProviderInvoice
	{
		return $this->qboProviderInvoice;
	}

	public function setQboProviderInvoice(?QboProviderInvoice $qboProviderInvoice): self
	{
		$this->qboProviderInvoice = $qboProviderInvoice;

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

	public function getTransactionType(): ?string
	{
		return $this->transactionType;
	}

	public function setTransactionType(?string $transactionType): self
	{
		$this->transactionType = $transactionType;

		return $this;
	}
}
