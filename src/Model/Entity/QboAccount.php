<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'qbo_account')]
#[ORM\Index(name: '', columns: ['qbo_account_id'])]
#[ORM\Entity]
class QboAccount implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'qbo_account_id', type: 'string', nullable: false)]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'sub_account', type: 'boolean', nullable: true)]
	private ?bool $subAccount;

	#[ORM\Column(name: 'parent_ref', type: 'string', nullable: true)]
	private ?string $parentRef;

	#[ORM\Column(name: 'fully_qualified_name', type: 'string', nullable: true)]
	private ?string $fullyQualifiedName;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'classification', type: 'string', nullable: true)]
	private ?string $classification;

	#[ORM\Column(name: 'account_type', type: 'string', nullable: true)]
	private ?string $accountType;

	#[ORM\Column(name: 'account_sub_type', type: 'string', nullable: true)]
	private ?string $accountSubType;

	#[ORM\Column(name: 'current_balance', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $currentBalance;

	#[ORM\Column(name: 'current_balance_with_sub_accounts', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $currentBalanceWithSubAccounts;

	#[ORM\Column(name: 'currency_ref', type: 'string', nullable: true)]
	private ?string $currencyRef;

	#[ORM\Column(name: 'metadata_created_by_ref', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataCreatedByRef;

	#[ORM\Column(name: 'metadata_create_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataCreateTime;

	#[ORM\Column(name: 'metadata_last_modified_by_ref', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastModifiedByRef;

	#[ORM\Column(name: 'metadata_last_updated_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastUpdatedTime;

	#[ORM\Column(name: 'metadata_last_changed_in_qb', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastChangedInQB;

	#[ORM\Column(name: 'metadata_synchronized', type: 'boolean', nullable: true)]
	private ?bool $metadataSynchronized;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getSubAccount(): ?bool
	{
		return $this->subAccount;
	}

	public function setSubAccount(?bool $subAccount): self
	{
		$this->subAccount = $subAccount;

		return $this;
	}

	public function getParentRef(): ?string
	{
		return $this->parentRef;
	}

	public function setParentRef(?string $parentRef): self
	{
		$this->parentRef = $parentRef;

		return $this;
	}

	public function getFullyQualifiedName(): ?string
	{
		return $this->fullyQualifiedName;
	}

	public function setFullyQualifiedName(?string $fullyQualifiedName): self
	{
		$this->fullyQualifiedName = $fullyQualifiedName;

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

	public function getClassification(): ?string
	{
		return $this->classification;
	}

	public function setClassification(?string $classification): self
	{
		$this->classification = $classification;

		return $this;
	}

	public function getAccountType(): ?string
	{
		return $this->accountType;
	}

	public function setAccountType(?string $accountType): self
	{
		$this->accountType = $accountType;

		return $this;
	}

	public function getAccountSubType(): ?string
	{
		return $this->accountSubType;
	}

	public function setAccountSubType(?string $accountSubType): self
	{
		$this->accountSubType = $accountSubType;

		return $this;
	}

	public function getCurrentBalance(): ?string
	{
		return $this->currentBalance;
	}

	public function setCurrentBalance(?string $currentBalance): self
	{
		$this->currentBalance = $currentBalance;

		return $this;
	}

	public function getCurrentBalanceWithSubAccounts(): ?string
	{
		return $this->currentBalanceWithSubAccounts;
	}

	public function setCurrentBalanceWithSubAccounts(?string $currentBalanceWithSubAccounts): self
	{
		$this->currentBalanceWithSubAccounts = $currentBalanceWithSubAccounts;

		return $this;
	}

	public function getCurrencyRef(): ?string
	{
		return $this->currencyRef;
	}

	public function setCurrencyRef(?string $currencyRef): self
	{
		$this->currencyRef = $currencyRef;

		return $this;
	}

	public function getMetadataCreatedByRef(): ?\DateTimeInterface
	{
		return $this->metadataCreatedByRef;
	}

	public function setMetadataCreatedByRef(?\DateTimeInterface $metadataCreatedByRef): self
	{
		$this->metadataCreatedByRef = $metadataCreatedByRef;

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

	public function getMetadataLastModifiedByRef(): ?\DateTimeInterface
	{
		return $this->metadataLastModifiedByRef;
	}

	public function setMetadataLastModifiedByRef(?\DateTimeInterface $metadataLastModifiedByRef): self
	{
		$this->metadataLastModifiedByRef = $metadataLastModifiedByRef;

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

	public function getMetadataLastChangedInQB(): ?\DateTimeInterface
	{
		return $this->metadataLastChangedInQB;
	}

	public function setMetadataLastChangedInQB(?\DateTimeInterface $metadataLastChangedInQB): self
	{
		$this->metadataLastChangedInQB = $metadataLastChangedInQB;

		return $this;
	}

	public function getMetadataSynchronized(): ?\DateTimeInterface
	{
		return $this->metadataSynchronized;
	}

	public function setMetadataSynchronized(?bool $metadataSynchronized): self
	{
		$this->metadataSynchronized = $metadataSynchronized;

		return $this;
	}
}
