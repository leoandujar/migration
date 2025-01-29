<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'provider_payment_item')]
#[ORM\Entity]
class ProviderPaymentItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'provider_payment_item_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'provider_payment_item_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $value;

	#[ORM\ManyToOne(targetEntity: ProviderCharge::class)]
	#[ORM\JoinColumn(name: 'provider_charge_id', referencedColumnName: 'provider_charge_id', nullable: false)]
	private ProviderCharge $providerCharge;

	#[ORM\ManyToOne(targetEntity: ProviderPayment::class, cascade: ['persist'], inversedBy: 'providerCharges')]
	#[ORM\JoinColumn(name: 'provider_payment_id', referencedColumnName: 'provider_payment_id', nullable: false)]
	private ProviderPayment $providerPayment;

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

	/**
	 * @return mixed
	 */
	public function getProviderCharge(): ?ProviderCharge
	{
		return $this->providerCharge;
	}

	/**
	 * @return mixed
	 */
	public function setProviderCharge(?ProviderCharge $providerCharge): self
	{
		$this->providerCharge = $providerCharge;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getProviderPayment(): ?ProviderPayment
	{
		return $this->providerPayment;
	}

	/**
	 * @return mixed
	 */
	public function setProviderPayment(?ProviderPayment $providerPayment): self
	{
		$this->providerPayment = $providerPayment;

		return $this;
	}
}
