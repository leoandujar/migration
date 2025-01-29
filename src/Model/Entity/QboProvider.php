<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'qbo_provider')]
#[ORM\Index(name: '', columns: ['qbo_provider_id'])]
#[ORM\Entity]
class QboProvider implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'qbo_provider_id', type: 'string', nullable: false)]
	private string $id;

	#[ORM\Column(name: 'email', type: 'string', nullable: true)]
	private ?string $email;

	#[ORM\Column(name: 'given_name', type: 'string', nullable: true)]
	private ?string $givenName;

	#[ORM\Column(name: 'display_name', type: 'string', nullable: true)]
	private ?string $displayName;

	#[ORM\Column(name: 'city', type: 'string', nullable: true)]
	private ?string $city;

	#[ORM\Column(name: 'address', type: 'string', nullable: true)]
	private ?string $address;

	#[ORM\Column(name: 'postal_code', type: 'string', nullable: true)]
	private ?string $postalCode;

	#[ORM\Column(name: 'lat', type: 'string', nullable: true)]
	private ?string $lat;

	#[ORM\Column(name: 'long', type: 'string', nullable: true)]
	private ?string $long;

	#[ORM\Column(name: 'state', type: 'string', nullable: true)]
	private ?string $state;

	#[ORM\Column(name: 'family_name', type: 'string', nullable: true)]
	private ?string $familyName;

	#[ORM\Column(name: 'phone', type: 'string', nullable: true)]
	private ?string $phone;

	#[ORM\Column(name: 'acct_num', type: 'string', nullable: true)]
	private ?string $acctNum;

	#[ORM\Column(name: 'company_name', type: 'string', nullable: true)]
	private ?string $companyName;

	#[ORM\Column(name: 'uri', type: 'string', nullable: true)]
	private ?string $uri;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'balance', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $balance;

	#[ORM\Column(name: 'metadata_create_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataCreateTime;

	#[ORM\Column(name: 'metadata_last_updated_time', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $metadataLastUpdatedTime;

	#[ORM\Column(name: 'category', type: 'string', nullable: true)]
	private ?string $category;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getGivenName(): ?string
	{
		return $this->givenName;
	}

	public function setGivenName(?string $givenName): self
	{
		$this->givenName = $givenName;

		return $this;
	}

	public function getDisplayName(): ?string
	{
		return $this->displayName;
	}

	public function setDisplayName(?string $displayName): self
	{
		$this->displayName = $displayName;

		return $this;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function setCity(?string $city): self
	{
		$this->city = $city;

		return $this;
	}

	public function getAddress(): ?string
	{
		return $this->address;
	}

	public function setAddress(?string $address): self
	{
		$this->address = $address;

		return $this;
	}

	public function getPostalCode(): ?string
	{
		return $this->postalCode;
	}

	public function setPostalCode(?string $postalCode): self
	{
		$this->postalCode = $postalCode;

		return $this;
	}

	public function getLat(): ?string
	{
		return $this->lat;
	}

	public function setLat(?string $lat): self
	{
		$this->lat = $lat;

		return $this;
	}

	public function getLong(): ?string
	{
		return $this->long;
	}

	public function setLong(?string $long): self
	{
		$this->long = $long;

		return $this;
	}

	public function getState(): ?string
	{
		return $this->state;
	}

	public function setState(?string $state): self
	{
		$this->state = $state;

		return $this;
	}

	public function getFamilyName(): ?string
	{
		return $this->familyName;
	}

	public function setFamilyName(?string $familyName): self
	{
		$this->familyName = $familyName;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): self
	{
		$this->phone = $phone;

		return $this;
	}

	public function getAcctNum(): ?string
	{
		return $this->acctNum;
	}

	public function setAcctNum(?string $acctNum): self
	{
		$this->acctNum = $acctNum;

		return $this;
	}

	public function getCompanyName(): ?string
	{
		return $this->companyName;
	}

	public function setCompanyName(?string $companyName): self
	{
		$this->companyName = $companyName;

		return $this;
	}

	public function getUri(): ?string
	{
		return $this->uri;
	}

	public function setUri(?string $uri): self
	{
		$this->uri = $uri;

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

	public function getBalance(): ?string
	{
		return $this->balance;
	}

	public function setBalance(?string $balance): self
	{
		$this->balance = $balance;

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

	public function getCategory(): ?string
	{
		return $this->category;
	}

	public function setCategory(?string $category): self
	{
		$this->category = $category;

		return $this;
	}
}
