<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'qbo_customer_payment')]
#[ORM\Index(name: '', columns: ['qbo_customer_payment_id'])]
#[ORM\Entity]
class QboCustomerPayment implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'qbo_customer_payment_id', type: 'string', nullable: false)]
	private string $id;

	#[ORM\Column(name: 'customer_id', type: 'string', nullable: true)]
	private ?string $customerId;

	#[ORM\Column(name: 'qbo_account_id', type: 'string', nullable: true)]
	private ?string $qboAccountId;

	#[ORM\Column(name: 'linked_transaction_id', type: 'string', nullable: true)]
	private ?string $linkedTransactionId;

	#[ORM\Column(name: 'linked_transaction_type', type: 'string', nullable: true)]
	private ?string $linkedTransactionType;

	#[ORM\Column(name: 'payment_method_id', type: 'string', nullable: true)]
	private ?string $paymentMethodId;

	#[ORM\Column(name: 'payment_ref_num', type: 'string', nullable: true)]
	private ?string $paymentRefNum;

	#[ORM\Column(name: 'total_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $totalAmount;

	#[ORM\Column(name: 'unapplied_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $unappliedAmount;

	#[ORM\Column(name: 'process_payment', type: 'string', nullable: true)]
	private ?string $processPayment;

	#[ORM\Column(name: 'transaction_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $transactionDate;

	#[ORM\Column(name: 'currency', type: 'string', nullable: true)]
	private ?string $currency;

	#[ORM\Column(name: 'exchange_rate', type: 'string', nullable: true)]
	private ?string $exchangeRate;

	#[ORM\Column(name: 'private_note', type: 'text', nullable: true)]
	private ?string $privateNote;

	#[ORM\Column(name: 'transaction_type', type: 'string', nullable: true)]
	private ?string $transactionType;

	#[ORM\Column(name: 'transaction_id', type: 'string', nullable: true)]
	private ?string $transactionId;

	#[ORM\Column(name: 'metadata_create_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataCreateTime;

	#[ORM\Column(name: 'metadata_last_updated_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastUpdatedTime;

	#[ORM\ManyToOne(targetEntity: CustomerPayment::class)]
	#[ORM\JoinColumn(name: 'xtrf_customer_payment_id', referencedColumnName: 'customer_payment_id', nullable: true)]
	private ?CustomerPayment $xtrfCustomerPayment;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getCustomerId(): ?string
	{
		return $this->customerId;
	}

	public function setCustomerId(?string $customerId): self
	{
		$this->customerId = $customerId;

		return $this;
	}

	public function getQboAccountId(): ?string
	{
		return $this->qboAccountId;
	}

	public function setQboAccountId(?string $qboAccountId): self
	{
		$this->qboAccountId = $qboAccountId;

		return $this;
	}

	public function getPaymentMethodId(): ?string
	{
		return $this->paymentMethodId;
	}

	public function setPaymentMethodId(?string $paymentMethodId): self
	{
		$this->paymentMethodId = $paymentMethodId;

		return $this;
	}

	public function getPaymentRefNum(): ?string
	{
		return $this->paymentRefNum;
	}

	public function setPaymentRefNum(?string $paymentRefNum): self
	{
		$this->paymentRefNum = $paymentRefNum;

		return $this;
	}

	public function getTotalAmount(): ?string
	{
		return $this->totalAmount;
	}

	public function setTotalAmount(?string $totalAmount): self
	{
		$this->totalAmount = $totalAmount;

		return $this;
	}

	public function getUnappliedAmount(): ?string
	{
		return $this->unappliedAmount;
	}

	public function setUnappliedAmount(?string $unappliedAmount): self
	{
		$this->unappliedAmount = $unappliedAmount;

		return $this;
	}

	public function getProcessPayment(): ?string
	{
		return $this->processPayment;
	}

	public function setProcessPayment(?string $processPayment): self
	{
		$this->processPayment = $processPayment;

		return $this;
	}

	public function getTransactionDate(): ?\DateTimeInterface
	{
		return $this->transactionDate;
	}

	public function setTransactionDate(?\DateTimeInterface $transactionDate): self
	{
		$this->transactionDate = $transactionDate;

		return $this;
	}

	public function getCurrency(): ?string
	{
		return $this->currency;
	}

	public function setCurrency(?string $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getExchangeRate(): ?string
	{
		return $this->exchangeRate;
	}

	public function setExchangeRate(?string $exchangeRate): self
	{
		$this->exchangeRate = $exchangeRate;

		return $this;
	}

	public function getPrivateNote(): ?string
	{
		return $this->privateNote;
	}

	public function setPrivateNote(?string $privateNote): self
	{
		$this->privateNote = $privateNote;

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

	public function getTransactionId(): ?string
	{
		return $this->transactionId;
	}

	public function setTransactionId(?string $transactionId): self
	{
		$this->transactionId = $transactionId;

		return $this;
	}

	public function getMetadataCreateTime(): ?\DateTimeInterface
	{
		return $this->metadataCreateTime;
	}

	public function setMetadataCreateTime(?\DateTimeInterface $metadataCreateTime): self
	{
		$this->metadataCreateTime = $metadataCreateTime;

		return $this;
	}

	public function getMetadataLastUpdatedTime(): ?\DateTimeInterface
	{
		return $this->metadataLastUpdatedTime;
	}

	public function setMetadataLastUpdatedTime(?\DateTimeInterface $metadataLastUpdatedTime): self
	{
		$this->metadataLastUpdatedTime = $metadataLastUpdatedTime;

		return $this;
	}

	public function getXtrfCustomerPayment(): ?CustomerPayment
	{
		return $this->xtrfCustomerPayment;
	}

	public function setXtrfCustomerPayment(?CustomerPayment $xtrfCustomerPayment): self
	{
		$this->xtrfCustomerPayment = $xtrfCustomerPayment;

		return $this;
	}

	public function getLinkedTransactionId(): ?string
	{
		return $this->linkedTransactionId;
	}

	public function setLinkedTransactionId(?string $linkedTransactionId): self
	{
		$this->linkedTransactionId = $linkedTransactionId;

		return $this;
	}

	public function getLinkedTransactionType(): ?string
	{
		return $this->linkedTransactionType;
	}

	public function setLinkedTransactionType(?string $linkedTransactionType): self
	{
		$this->linkedTransactionType = $linkedTransactionType;

		return $this;
	}
}
