<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_price_profile')]
#[ORM\UniqueConstraint(name: 'customer_price_profile_name_customer_id_key', columns: ['name', 'customer_id'])]
#[ORM\Entity]
class CustomerPriceProfile implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_price_profile_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_price_profile_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'default_tm_cat_tool', type: 'string', nullable: true)]
	private ?string $defaultTmCatTool;

	#[ORM\Column(name: 'default_tm_rates_type', type: 'string', nullable: true)]
	private ?string $defaultTmRatesType;

	#[ORM\Column(name: 'description', type: 'string', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'is_default', type: 'boolean', nullable: true)]
	private ?bool $isDefault;

	#[ORM\Column(name: 'manual_amount_modifier_name', type: 'text', nullable: true)]
	private ?string $manualAmountModifierName;

	#[ORM\Column(name: 'minimal_charge', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $minimalCharge;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'total_amount_modifier', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $totalAmountModifier;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'default_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'default_contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $defaultContactPerson;

	#[ORM\ManyToOne(targetEntity: CustomerPriceList::class, inversedBy: 'priceProfile')]
	#[ORM\JoinColumn(name: 'price_list_id', referencedColumnName: 'customer_price_list_id', nullable: true)]
	private ?CustomerPriceList $priceList;

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

	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(?bool $active): self
	{
		$this->active = $active;

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

	public function getDefaultTmCatTool(): ?string
	{
		return $this->defaultTmCatTool;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultTmCatTool(?string $defaultTmCatTool): self
	{
		$this->defaultTmCatTool = $defaultTmCatTool;

		return $this;
	}

	public function getDefaultTmRatesType(): ?string
	{
		return $this->defaultTmRatesType;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultTmRatesType(?string $defaultTmRatesType): self
	{
		$this->defaultTmRatesType = $defaultTmRatesType;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getIsDefault(): ?bool
	{
		return $this->isDefault;
	}

	/**
	 * @return mixed
	 */
	public function setIsDefault(?bool $isDefault): self
	{
		$this->isDefault = $isDefault;

		return $this;
	}

	public function getManualAmountModifierName(): ?string
	{
		return $this->manualAmountModifierName;
	}

	/**
	 * @return mixed
	 */
	public function setManualAmountModifierName(?string $manualAmountModifierName): self
	{
		$this->manualAmountModifierName = $manualAmountModifierName;

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

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getTotalAmountModifier(): ?string
	{
		return $this->totalAmountModifier;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAmountModifier(?string $totalAmountModifier): self
	{
		$this->totalAmountModifier = $totalAmountModifier;

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

	public function getDefaultContactPerson(): ?CustomerPerson
	{
		return $this->defaultContactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultContactPerson(?CustomerPerson $defaultContactPerson): self
	{
		$this->defaultContactPerson = $defaultContactPerson;

		return $this;
	}

	public function getPriceList(): ?CustomerPriceList
	{
		return $this->priceList;
	}

	/**
	 * @return mixed
	 */
	public function setPriceList(?CustomerPriceList $priceList): self
	{
		$this->priceList = $priceList;

		return $this;
	}
}
