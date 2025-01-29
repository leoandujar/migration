<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'qbo_provider_payment')]
#[ORM\Index(name: '', columns: ['qbo_provider_payment_id'])]
#[ORM\Entity]
class QboProviderPayment implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'qbo_provider_payment_id', type: 'string', nullable: false)]
	private string $id;

	#[ORM\Column(name: 'provider_id', type: 'string', nullable: true)]
	private ?string $providerId;

	#[ORM\Column(name: 'pay_type', type: 'string', nullable: true)]
	private ?string $payType;

	#[ORM\Column(name: 'doc_number', type: 'string', nullable: true)]
	private ?string $docNumber;

	#[ORM\Column(name: 'qbo_account_id', type: 'string', nullable: true)]
	private ?string $qboAccountId;

	#[ORM\Column(name: 'total_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $totalAmount;

	#[ORM\Column(name: 'transaction_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $transactionDate;

	#[ORM\Column(name: 'currency', type: 'string', nullable: true)]
	private ?string $currency;

	#[ORM\Column(name: 'metadata_create_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataCreateTime;

	#[ORM\Column(name: 'metadata_last_updated_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastUpdatedTime;

	#[ORM\OneToOne(targetEntity: ProviderPayment::class)]
	#[ORM\JoinColumn(name: 'xtrf_provider_payment_id', referencedColumnName: 'provider_payment_id', nullable: true)]
	private ?ProviderPayment $xtrfProviderPayment;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getProviderId(): ?string
	{
		return $this->providerId;
	}

	public function setProviderId(?string $providerId): self
	{
		$this->providerId = $providerId;

		return $this;
	}

	public function getPayType(): ?string
	{
		return $this->payType;
	}

	public function setPayType(?string $payType): self
	{
		$this->payType = $payType;

		return $this;
	}

	public function getDocNumber(): ?string
	{
		return $this->docNumber;
	}

	public function setDocNumber(?string $docNumber): self
	{
		$this->docNumber = $docNumber;

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

	public function getTotalAmount(): ?string
	{
		return $this->totalAmount;
	}

	public function setTotalAmount(?string $totalAmount): self
	{
		$this->totalAmount = $totalAmount;

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

	public function getXtrfProviderPayment(): ?ProviderPayment
	{
		return $this->xtrfProviderPayment;
	}

	public function setXtrfProviderPayment(?ProviderPayment $xtrfProviderPayment): self
	{
		$this->xtrfProviderPayment = $xtrfProviderPayment;

		return $this;
	}
}
