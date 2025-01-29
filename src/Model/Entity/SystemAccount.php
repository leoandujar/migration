<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'system_account')]
#[ORM\UniqueConstraint(name: 'system_account_uid_key', columns: ['uid'])]
#[ORM\Entity]
class SystemAccount implements EntityInterface
{
	public const OFFICE_ALL_OFFICE = 'ALL_DEFINED_OFFICES';
	public const OFFICE_DEPARTMENT = 'DEPARTMENT';
	public const OFFICE_OFFICE = 'OFFICE';
	public const OFFICE_ONLY_RELATED = 'ONLY_RELATED_QUOTES_AND_PROJECTS';
	public const OFFICE_ALL_OFFICE_RELATED = 'ALL_DEFINED_OFFICE_RELATED';
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'system_account_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'system_account_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'enableflag', type: 'boolean', nullable: false)]
	private bool $enableflag;

	#[ORM\Column(name: 'writepermission', type: 'boolean', nullable: false)]
	private bool $writepermission;

	#[ORM\Column(name: 'homedirectory', type: 'string', nullable: true)]
	private ?string $homedirectory;

	#[ORM\Column(name: 'uid', type: 'string', nullable: false)]
	private string $uid;

	#[ORM\Column(name: 'userpassword', type: 'string', nullable: true)]
	private ?string $userpassword;

	#[ORM\Column(name: 'shell', type: 'string', nullable: true)]
	private ?string $shell;

	#[ORM\Column(name: 'web_login_allowed', type: 'boolean', nullable: false)]
	private bool $webLoginAllowed;

	#[ORM\Column(name: 'customer_contact_manage_policy', type: 'string', nullable: false)]
	private string $customerContactManagePolicy;

	#[ORM\Column(name: 'cp_scope', type: 'string', nullable: true, options: ['default' => self::OFFICE_OFFICE])]
	private ?string $cpScope;

	#[ORM\Column(name: 'customer_contact_can_accept_and_reject_quote', type: 'boolean', nullable: false, options: ['default' => true])]
	private bool $customerContactCanAcceptAndRejectQuote;

	#[ORM\Column(name: 'cp_api_password', type: 'string', length: 64, nullable: true)]
	private ?string $cpApiPassword;

	#[ORM\Column(name: 'password_updated_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $passwordUpdatedAt;

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

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getEnableflag(): ?bool
	{
		return $this->enableflag;
	}

	public function setEnableflag(bool $enableflag): self
	{
		$this->enableflag = $enableflag;

		return $this;
	}

	public function getWritepermission(): ?bool
	{
		return $this->writepermission;
	}

	public function setWritepermission(bool $writepermission): self
	{
		$this->writepermission = $writepermission;

		return $this;
	}

	public function getHomedirectory(): ?string
	{
		return $this->homedirectory;
	}

	public function setHomedirectory(?string $homedirectory): self
	{
		$this->homedirectory = $homedirectory;

		return $this;
	}

	public function getUid(): ?string
	{
		return $this->uid;
	}

	public function setUid(string $uid): self
	{
		$this->uid = $uid;

		return $this;
	}

	public function getUserpassword(): ?string
	{
		return $this->userpassword;
	}

	public function setUserpassword(?string $userpassword): self
	{
		$this->userpassword = $userpassword;

		return $this;
	}

	public function getShell(): ?string
	{
		return $this->shell;
	}

	public function setShell(?string $shell): self
	{
		$this->shell = $shell;

		return $this;
	}

	public function getWebLoginAllowed(): ?bool
	{
		return $this->webLoginAllowed;
	}

	public function setWebLoginAllowed(bool $webLoginAllowed): self
	{
		$this->webLoginAllowed = $webLoginAllowed;

		return $this;
	}

	public function getCustomerContactManagePolicy(): ?string
	{
		return $this->customerContactManagePolicy;
	}

	public function setCustomerContactManagePolicy(string $customerContactManagePolicy): self
	{
		$this->customerContactManagePolicy = $customerContactManagePolicy;

		return $this;
	}

	public function getCustomerContactCanAcceptAndRejectQuote(): ?bool
	{
		return $this->customerContactCanAcceptAndRejectQuote;
	}

	public function setCustomerContactCanAcceptAndRejectQuote(bool $customerContactCanAcceptAndRejectQuote): self
	{
		$this->customerContactCanAcceptAndRejectQuote = $customerContactCanAcceptAndRejectQuote;

		return $this;
	}

	public function getCpApiPassword(): ?string
	{
		return $this->cpApiPassword;
	}

	public function setCpApiPassword(?string $cpApiPassword): self
	{
		$this->cpApiPassword = $cpApiPassword;

		return $this;
	}

	public function isEnableflag(): ?bool
	{
		return $this->enableflag;
	}

	public function isWritepermission(): ?bool
	{
		return $this->writepermission;
	}

	public function isWebLoginAllowed(): ?bool
	{
		return $this->webLoginAllowed;
	}

	public function getCpScope(): ?string
	{
		return $this->cpScope;
	}

	public function setCpScope(?string $cpScope): static
	{
		$this->cpScope = $cpScope;

		return $this;
	}

	public function isCustomerContactCanAcceptAndRejectQuote(): ?bool
	{
		return $this->customerContactCanAcceptAndRejectQuote;
	}

	public function getPasswordUpdatedAt(): ?\DateTimeInterface
	{
		return $this->passwordUpdatedAt;
	}

	public function setPasswordUpdatedAt(?\DateTimeInterface $passwordUpdatedAt): static
	{
		$this->passwordUpdatedAt = $passwordUpdatedAt;

		return $this;
	}
}
