<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'qbo_provider_invoice')]
#[ORM\Index(name: '', columns: ['qbo_provider_invoice_id'])]
#[ORM\Entity]
class QboProviderInvoice implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'qbo_provider_invoice_id', type: 'string', nullable: false)]
	private string $id;

	#[ORM\Column(name: 'qbo_account_id', type: 'string', nullable: true)]
	private ?string $qboAccountId;

	#[ORM\Column(name: 'final_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $finalDate;

	#[ORM\Column(name: 'total_netto', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $totalNetto;

	#[ORM\Column(name: 'currency_id', type: 'string', nullable: true)]
	private ?string $currency;

	#[ORM\Column(name: 'required_payment_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $requiredPaymentDate;

	#[ORM\Column(name: 'balance', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $balance;

	#[ORM\Column(name: 'created_on_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdOnDate;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\ManyToOne(targetEntity: QboProvider::class)]
	#[ORM\JoinColumn(name: 'qbo_provider_id', referencedColumnName: 'qbo_provider_id', nullable: true)]
	private ?QboProvider $qboProvider;

	#[ORM\ManyToOne(targetEntity: Provider::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'xtrf_provider_id', referencedColumnName: 'provider_id', nullable: true)]
	private ?Provider $xtrfProvider;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

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

	public function getFinalDate(): ?\DateTimeInterface
	{
		return $this->finalDate;
	}

	public function setFinalDate(?\DateTimeInterface $finalDate): self
	{
		$this->finalDate = $finalDate;

		return $this;
	}

	public function getTotalNetto(): ?string
	{
		return $this->totalNetto;
	}

	public function setTotalNetto(?string $totalNetto): self
	{
		$this->totalNetto = $totalNetto;

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

	public function getRequiredPaymentDate(): ?\DateTimeInterface
	{
		return $this->requiredPaymentDate;
	}

	public function setRequiredPaymentDate(?\DateTimeInterface $requiredPaymentDate): self
	{
		$this->requiredPaymentDate = $requiredPaymentDate;

		return $this;
	}

	public function getBalance(): ?string
	{
		return $this->balance;
	}

	public function setBalance(?string $balance): self
	{
		$this->balance = $balance;

		return $this;
	}

	public function getCreatedOnDate(): ?\DateTimeInterface
	{
		return $this->createdOnDate;
	}

	public function setCreatedOnDate(?\DateTimeInterface $createdOnDate): self
	{
		$this->createdOnDate = $createdOnDate;

		return $this;
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

	public function getQboProvider(): ?QboProvider
	{
		return $this->qboProvider;
	}

	public function setQboProvider(?QboProvider $qboProvider): self
	{
		$this->qboProvider = $qboProvider;

		return $this;
	}

	public function getXtrfProvider(): ?Provider
	{
		return $this->xtrfProvider;
	}

	public function setXtrfProvider(?Provider $xtrfProvider): self
	{
		$this->xtrfProvider = $xtrfProvider;

		return $this;
	}
}
