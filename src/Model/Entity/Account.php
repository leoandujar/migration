<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'account')]
#[ORM\UniqueConstraint(name: 'account_name_customer_id_provider_id_key', columns: ['name', 'customer_id', 'provider_id'])]
#[ORM\Entity]
class Account implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'account_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'account_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'abi', type: 'string', nullable: true)]
	private ?string $abi;

	#[ORM\Column(name: 'cab', type: 'string', nullable: true)]
	private ?string $cab;

	#[ORM\Column(name: 'cin', type: 'string', nullable: true)]
	private ?string $cin;

	#[ORM\Column(name: 'account_number', type: 'string', nullable: true)]
	private ?string $accountNumber;

	#[ORM\Column(name: 'account_owner_address_address', type: 'string', nullable: true)]
	private ?string $accountOwnerAddressAddress;

	#[ORM\Column(name: 'account_owner_address_address_2', type: 'string', nullable: true)]
	private ?string $accountOwnerAddressAddress2;

	#[ORM\Column(name: 'account_owner_address_city', type: 'string', nullable: true)]
	private ?string $accountOwnerAddressCity;

	#[ORM\Column(name: 'account_owner_dependent_locality', type: 'string', nullable: true)]
	private ?string $accountOwnerDependentLocality;

	#[ORM\Column(name: 'account_owner_sorting_code', type: 'string', nullable: true)]
	private ?string $accountOwnerSortingCode;

	#[ORM\Column(name: 'account_owner_address_zip_code', type: 'string', nullable: true)]
	private ?string $accountOwnerAddressZipCode;

	#[ORM\Column(name: 'account_owner_name', type: 'string', nullable: false)]
	private string $accountOwnerName;

	#[ORM\Column(name: 'bank_address_address', type: 'string', nullable: true)]
	private ?string $bankAddressAddress;

	#[ORM\Column(name: 'bank_address_address_2', type: 'string', nullable: true)]
	private ?string $bankAddressAddress2;

	#[ORM\Column(name: 'bank_address_city', type: 'string', nullable: true)]
	private ?string $bankAddressCity;

	#[ORM\Column(name: 'bank_address_dependent_locality', type: 'string', nullable: true)]
	private ?string $bankAddressDependentLocality;

	#[ORM\Column(name: 'bank_address_sorting_code', type: 'string', nullable: true)]
	private ?string $bankAddressSortingCode;

	#[ORM\Column(name: 'bank_address_zip_code', type: 'string', nullable: true)]
	private ?string $bankAddressZipCode;

	#[ORM\Column(name: 'bank_name', type: 'string', nullable: true)]
	private ?string $bankName;

	#[ORM\Column(name: 'iban_number', type: 'string', nullable: true)]
	private ?string $ibanNumber;

	#[ORM\Column(name: 'sort_code', type: 'string', nullable: true)]
	private ?string $sortCode;

	#[ORM\Column(name: 'swift', type: 'string', nullable: true)]
	private ?string $swift;

	#[ORM\ManyToOne(targetEntity: Country::class)]
	#[ORM\JoinColumn(name: 'account_owner_address_country_id', referencedColumnName: 'country_id', nullable: true)]
	private ?Country $accountOwnerAddressCountry;

	#[ORM\ManyToOne(targetEntity: Province::class)]
	#[ORM\JoinColumn(name: 'account_owner_address_province_id', referencedColumnName: 'province_id', nullable: true)]
	private ?Province $accountOwnerAddressProvince;

	#[ORM\ManyToOne(targetEntity: Country::class)]
	#[ORM\JoinColumn(name: 'bank_address_country_id', referencedColumnName: 'country_id', nullable: true)]
	private ?Country $bankAddressCountry;

	#[ORM\ManyToOne(targetEntity: Province::class)]
	#[ORM\JoinColumn(name: 'bank_address_province_id', referencedColumnName: 'province_id', nullable: true)]
	private ?Province $bankAddressProvince;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'xtrf_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $currency;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\Column(name: 'customer_id', type: 'bigint', nullable: true)]
	private ?string $customField;

	#[ORM\Column(name: 'payment_method_type_id', type: 'bigint', nullable: true)]
	private ?string $paymentMethodTypeId;

	#[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'accounts')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: true)]
	private ?Provider $provider;

	#[ORM\Column(name: 'intermediary_bank', type: 'text', nullable: true)]
	private ?string $intermediaryBank;

	#[ORM\Column(name: 'custom_field_id', type: 'bigint', nullable: true)]
	private ?string $customFieldId;

	#[ORM\ManyToMany(targetEntity: Branch::class, mappedBy: 'branchesAvailable', cascade: ['persist'])]
	private mixed $paymentsMethods;

	#[ORM\ManyToMany(targetEntity: Branch::class, mappedBy: 'branchesDefault', cascade: ['persist'])]
	private mixed $defaultPaymentsMethods;

	public function __construct()
	{
		$this->paymentsMethods        = new ArrayCollection();
		$this->defaultPaymentsMethods = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
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

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	public function setDefaultEntity(bool $defaultEntity): self
	{
		$this->defaultEntity = $defaultEntity;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	public function setPreferedEntity(bool $preferedEntity): self
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	public function getAbi(): ?string
	{
		return $this->abi;
	}

	public function setAbi(?string $abi): self
	{
		$this->abi = $abi;

		return $this;
	}

	public function getCab(): ?string
	{
		return $this->cab;
	}

	public function setCab(?string $cab): self
	{
		$this->cab = $cab;

		return $this;
	}

	public function getCin(): ?string
	{
		return $this->cin;
	}

	public function setCin(?string $cin): self
	{
		$this->cin = $cin;

		return $this;
	}

	public function getAccountNumber(): ?string
	{
		return $this->accountNumber;
	}

	public function setAccountNumber(?string $accountNumber): self
	{
		$this->accountNumber = $accountNumber;

		return $this;
	}

	public function getAccountOwnerAddressAddress(): ?string
	{
		return $this->accountOwnerAddressAddress;
	}

	public function setAccountOwnerAddressAddress(?string $accountOwnerAddressAddress): self
	{
		$this->accountOwnerAddressAddress = $accountOwnerAddressAddress;

		return $this;
	}

	public function getAccountOwnerAddressAddress2(): ?string
	{
		return $this->accountOwnerAddressAddress2;
	}

	public function setAccountOwnerAddressAddress2(?string $accountOwnerAddressAddress2): self
	{
		$this->accountOwnerAddressAddress2 = $accountOwnerAddressAddress2;

		return $this;
	}

	public function getAccountOwnerAddressCity(): ?string
	{
		return $this->accountOwnerAddressCity;
	}

	public function setAccountOwnerAddressCity(?string $accountOwnerAddressCity): self
	{
		$this->accountOwnerAddressCity = $accountOwnerAddressCity;

		return $this;
	}

	public function getAccountOwnerDependentLocality(): ?string
	{
		return $this->accountOwnerDependentLocality;
	}

	public function setAccountOwnerDependentLocality(?string $accountOwnerDependentLocality): self
	{
		$this->accountOwnerDependentLocality = $accountOwnerDependentLocality;

		return $this;
	}

	public function getAccountOwnerSortingCode(): ?string
	{
		return $this->accountOwnerSortingCode;
	}

	public function setAccountOwnerSortingCode(?string $accountOwnerSortingCode): self
	{
		$this->accountOwnerSortingCode = $accountOwnerSortingCode;

		return $this;
	}

	public function getAccountOwnerAddressZipCode(): ?string
	{
		return $this->accountOwnerAddressZipCode;
	}

	public function setAccountOwnerAddressZipCode(?string $accountOwnerAddressZipCode): self
	{
		$this->accountOwnerAddressZipCode = $accountOwnerAddressZipCode;

		return $this;
	}

	public function getAccountOwnerName(): ?string
	{
		return $this->accountOwnerName;
	}

	public function setAccountOwnerName(string $accountOwnerName): self
	{
		$this->accountOwnerName = $accountOwnerName;

		return $this;
	}

	public function getBankAddressAddress(): ?string
	{
		return $this->bankAddressAddress;
	}

	public function setBankAddressAddress(?string $bankAddressAddress): self
	{
		$this->bankAddressAddress = $bankAddressAddress;

		return $this;
	}

	public function getBankAddressAddress2(): ?string
	{
		return $this->bankAddressAddress2;
	}

	public function setBankAddressAddress2(?string $bankAddressAddress2): self
	{
		$this->bankAddressAddress2 = $bankAddressAddress2;

		return $this;
	}

	public function getBankAddressCity(): ?string
	{
		return $this->bankAddressCity;
	}

	public function setBankAddressCity(?string $bankAddressCity): self
	{
		$this->bankAddressCity = $bankAddressCity;

		return $this;
	}

	public function getBankAddressDependentLocality(): ?string
	{
		return $this->bankAddressDependentLocality;
	}

	public function setBankAddressDependentLocality(?string $bankAddressDependentLocality): self
	{
		$this->bankAddressDependentLocality = $bankAddressDependentLocality;

		return $this;
	}

	public function getBankAddressSortingCode(): ?string
	{
		return $this->bankAddressSortingCode;
	}

	public function setBankAddressSortingCode(?string $bankAddressSortingCode): self
	{
		$this->bankAddressSortingCode = $bankAddressSortingCode;

		return $this;
	}

	public function getBankAddressZipCode(): ?string
	{
		return $this->bankAddressZipCode;
	}

	public function setBankAddressZipCode(?string $bankAddressZipCode): self
	{
		$this->bankAddressZipCode = $bankAddressZipCode;

		return $this;
	}

	public function getBankName(): ?string
	{
		return $this->bankName;
	}

	public function setBankName(?string $bankName): self
	{
		$this->bankName = $bankName;

		return $this;
	}

	public function getIbanNumber(): ?string
	{
		return $this->ibanNumber;
	}

	public function setIbanNumber(?string $ibanNumber): self
	{
		$this->ibanNumber = $ibanNumber;

		return $this;
	}

	public function getSortCode(): ?string
	{
		return $this->sortCode;
	}

	public function setSortCode(?string $sortCode): self
	{
		$this->sortCode = $sortCode;

		return $this;
	}

	public function getSwift(): ?string
	{
		return $this->swift;
	}

	public function setSwift(?string $swift): self
	{
		$this->swift = $swift;

		return $this;
	}

	public function getCustomField(): ?string
	{
		return $this->customField;
	}

	public function setCustomField(?string $customField): self
	{
		$this->customField = $customField;

		return $this;
	}

	public function getPaymentMethodTypeId(): ?string
	{
		return $this->paymentMethodTypeId;
	}

	public function setPaymentMethodTypeId(?string $paymentMethodTypeId): self
	{
		$this->paymentMethodTypeId = $paymentMethodTypeId;

		return $this;
	}

	public function getIntermediaryBank(): ?string
	{
		return $this->intermediaryBank;
	}

	public function setIntermediaryBank(?string $intermediaryBank): self
	{
		$this->intermediaryBank = $intermediaryBank;

		return $this;
	}

	public function getCustomFieldId(): ?string
	{
		return $this->customFieldId;
	}

	public function setCustomFieldId(?string $customFieldId): self
	{
		$this->customFieldId = $customFieldId;

		return $this;
	}

	public function getAccountOwnerAddressCountry(): ?Country
	{
		return $this->accountOwnerAddressCountry;
	}

	public function setAccountOwnerAddressCountry(?Country $accountOwnerAddressCountry): self
	{
		$this->accountOwnerAddressCountry = $accountOwnerAddressCountry;

		return $this;
	}

	public function getAccountOwnerAddressProvince(): ?Province
	{
		return $this->accountOwnerAddressProvince;
	}

	public function setAccountOwnerAddressProvince(?Province $accountOwnerAddressProvince): self
	{
		$this->accountOwnerAddressProvince = $accountOwnerAddressProvince;

		return $this;
	}

	public function getBankAddressCountry(): ?Country
	{
		return $this->bankAddressCountry;
	}

	public function setBankAddressCountry(?Country $bankAddressCountry): self
	{
		$this->bankAddressCountry = $bankAddressCountry;

		return $this;
	}

	public function getBankAddressProvince(): ?Province
	{
		return $this->bankAddressProvince;
	}

	public function setBankAddressProvince(?Province $bankAddressProvince): self
	{
		$this->bankAddressProvince = $bankAddressProvince;

		return $this;
	}

	public function getCurrency(): ?Currency
	{
		return $this->currency;
	}

	public function setCurrency(?Currency $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getProvider(): ?Provider
	{
		return $this->provider;
	}

	public function setProvider(?Provider $provider): self
	{
		$this->provider = $provider;

		return $this;
	}

	public function getPaymentsMethods(): Collection
	{
		return $this->paymentsMethods;
	}

	public function addPaymentsMethod(Branch $paymentsMethod): self
	{
		if (!$this->paymentsMethods->contains($paymentsMethod)) {
			$this->paymentsMethods[] = $paymentsMethod;
			$paymentsMethod->addBranchesAvailable($this);
		}

		return $this;
	}

	public function removePaymentsMethod(Branch $paymentsMethod): self
	{
		if ($this->paymentsMethods->removeElement($paymentsMethod)) {
			$paymentsMethod->removeBranchesAvailable($this);
		}

		return $this;
	}

	public function getDefaultPaymentsMethods(): Collection
	{
		return $this->defaultPaymentsMethods;
	}

	public function addDefaultPaymentsMethod(Branch $defaultPaymentsMethod): self
	{
		if (!$this->defaultPaymentsMethods->contains($defaultPaymentsMethod)) {
			$this->defaultPaymentsMethods[] = $defaultPaymentsMethod;
			$defaultPaymentsMethod->addBranchesDefault($this);
		}

		return $this;
	}

	public function removeDefaultPaymentsMethod(Branch $defaultPaymentsMethod): self
	{
		if ($this->defaultPaymentsMethods->removeElement($defaultPaymentsMethod)) {
			$defaultPaymentsMethod->removeBranchesDefault($this);
		}

		return $this;
	}
}
