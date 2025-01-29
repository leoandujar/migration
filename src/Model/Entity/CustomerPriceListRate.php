<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_price_list_rate')]
#[ORM\Entity()]
class CustomerPriceListRate implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_price_list_rate_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_price_list_rate_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'bigint', nullable: false)]
	private string $version;

	#[ORM\Column(name: 'minimal_charge', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $minimalCharge;

	#[ORM\Column(name: 'rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $rate;

	#[ORM\ManyToOne(targetEntity: ActivityType::class)]
	#[ORM\JoinColumn(name: 'activity_type_id', referencedColumnName: 'activity_type_id', nullable: false)]
	private ActivityType $activityType;

	#[ORM\ManyToOne(targetEntity: CalculationUnit::class)]
	#[ORM\JoinColumn(name: 'calculation_unit_id', referencedColumnName: 'calculation_unit_id', nullable: false)]
	private CalculationUnit $calculationUnit;

	#[ORM\ManyToOne(targetEntity: TmRate::class)]
	#[ORM\JoinColumn(name: 'tm_rates_id', referencedColumnName: 'tm_rates_id', nullable: true)]
	private ?TmRate $tmRates;

	#[ORM\ManyToOne(targetEntity: CustomerPriceListLanguageCombination::class)]
	#[ORM\JoinColumn(name: 'customer_price_list_language_combination_id', referencedColumnName: 'customer_price_list_language_combination_id', nullable: false)]
	private CustomerPriceListLanguageCombination $customerLanguageCombination;

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

	public function getVersion(): ?string
	{
		return $this->version;
	}

	/**
	 * @return mixed
	 */
	public function setVersion(string $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getMinimalCharge(): ?string
	{
		return $this->minimalCharge;
	}

	/**
	 * @return mixed
	 */
	public function setMinimalCharge(?string $minimalCharge): self
	{
		$this->minimalCharge = $minimalCharge;

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

	public function getActivityType(): ?ActivityType
	{
		return $this->activityType;
	}

	/**
	 * @return mixed
	 */
	public function setActivityType(?ActivityType $activityType): self
	{
		$this->activityType = $activityType;

		return $this;
	}

	public function getCalculationUnit(): ?CalculationUnit
	{
		return $this->calculationUnit;
	}

	/**
	 * @return mixed
	 */
	public function setCalculationUnit(?CalculationUnit $calculationUnit): self
	{
		$this->calculationUnit = $calculationUnit;

		return $this;
	}

	public function getTmRates(): ?TmRate
	{
		return $this->tmRates;
	}

	/**
	 * @return mixed
	 */
	public function setTmRates(?TmRate $tmRates): self
	{
		$this->tmRates = $tmRates;

		return $this;
	}

	public function getCustomerLanguageCombination(): ?CustomerPriceListLanguageCombination
	{
		return $this->customerLanguageCombination;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerLanguageCombination(?CustomerPriceListLanguageCombination $customerLanguageCombination): self
	{
		$this->customerLanguageCombination = $customerLanguageCombination;

		return $this;
	}
}
