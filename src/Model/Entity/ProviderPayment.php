<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'provider_payment')]
#[ORM\Entity]
class ProviderPayment implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'provider_payment_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'provider_payment_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'accepted_value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $acceptedValue;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private string $notes;

	#[ORM\Column(name: 'payment_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $paymentDate;

	#[ORM\Column(name: 'received_value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $receivedValue;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'accepted_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $acceptedCurrency;

	#[ORM\Column(name: 'payment_method_id', type: 'bigint', nullable: true)]
	private ?string $paymentMethodId;

	#[ORM\Column(name: 'financial_system_id', type: 'bigint', nullable: true)]
	private ?string $financialSystemId;

	#[ORM\Column(name: 'financial_system_payment_id', type: 'string', nullable: true)]
	private ?string $financialSystemPaymentId;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'received_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $receivedCurrency;

	#[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'providerPayments')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: false)]
	private Provider $provider;

	#[ORM\OneToMany(targetEntity: ProviderPaymentItem::class, mappedBy: 'providerPayment', orphanRemoval: true)]
	private mixed $providerCharges;

	public function __construct()
	{
		$this->providerCharges = new ArrayCollection();
	}

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
	public function getAcceptedValue(): ?string
	{
		return $this->acceptedValue;
	}

	/**
	 * @return mixed
	 */
	public function setAcceptedValue(string $acceptedValue): self
	{
		$this->acceptedValue = $acceptedValue;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getNotes(): ?string
	{
		return $this->notes;
	}

	/**
	 * @return mixed
	 */
	public function setNotes(?string $notes): self
	{
		$this->notes = $notes;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentDate(): ?\DateTimeInterface
	{
		return $this->paymentDate;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentDate(?\DateTimeInterface $paymentDate): self
	{
		$this->paymentDate = $paymentDate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReceivedValue(): ?string
	{
		return $this->receivedValue;
	}

	/**
	 * @return mixed
	 */
	public function setReceivedValue(string $receivedValue): self
	{
		$this->receivedValue = $receivedValue;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentMethodId(): ?string
	{
		return $this->paymentMethodId;
	}

	/**
	 * @return mixed
	 */
	public function setPaymentMethodId(?string $paymentMethodId): self
	{
		$this->paymentMethodId = $paymentMethodId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFinancialSystemId(): ?string
	{
		return $this->financialSystemId;
	}

	/**
	 * @return mixed
	 */
	public function setFinancialSystemId(?string $financialSystemId): self
	{
		$this->financialSystemId = $financialSystemId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFinancialSystemPaymentId(): ?string
	{
		return $this->financialSystemPaymentId;
	}

	/**
	 * @return mixed
	 */
	public function setFinancialSystemPaymentId(?string $financialSystemPaymentId): self
	{
		$this->financialSystemPaymentId = $financialSystemPaymentId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAcceptedCurrency(): ?Currency
	{
		return $this->acceptedCurrency;
	}

	/**
	 * @return mixed
	 */
	public function setAcceptedCurrency(?Currency $acceptedCurrency): self
	{
		$this->acceptedCurrency = $acceptedCurrency;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReceivedCurrency(): ?Currency
	{
		return $this->receivedCurrency;
	}

	/**
	 * @return mixed
	 */
	public function setReceivedCurrency(?Currency $receivedCurrency): self
	{
		$this->receivedCurrency = $receivedCurrency;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getProvider(): ?Provider
	{
		return $this->provider;
	}

	/**
	 * @return mixed
	 */
	public function setProvider(?Provider $provider): self
	{
		$this->provider = $provider;

		return $this;
	}

	/**
	 * @return Collection|ProviderPaymentItem[]
	 */
	public function getProviderCharges(): Collection
	{
		return $this->providerCharges;
	}

	/**
	 * @return mixed
	 */
	public function addProviderCharge(ProviderPaymentItem $providerCharge): self
	{
		if (!$this->providerCharges->contains($providerCharge)) {
			$this->providerCharges[] = $providerCharge;
			$providerCharge->setProviderPayment($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProviderCharge(ProviderPaymentItem $providerCharge): self
	{
		if ($this->providerCharges->removeElement($providerCharge)) {
			// set the owning side to null (unless already changed)
			if ($providerCharge->getProviderPayment() === $this) {
				$providerCharge->setProviderPayment(null);
			}
		}

		return $this;
	}
}
