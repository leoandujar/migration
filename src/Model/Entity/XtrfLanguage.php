<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'xtrf_language')]
#[ORM\UniqueConstraint(name: 'xtrf_language_name_key', columns: ['name'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\XtrfLanguageRepository')]
class XtrfLanguage implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'xtrf_language_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'langiso', type: 'string', nullable: false)]
	private string $langiso;

	#[ORM\Column(name: 'langiso3', type: 'string', nullable: true)]
	private ?string $langiso3;

	#[ORM\Column(name: 'symbol', type: 'string', nullable: false)]
	private string $symbol;

	#[ORM\Column(name: 'multiterm_alias', type: 'string', nullable: true)]
	private ?string $multitermAlias;

	#[ORM\Column(name: 'language_code', type: 'string', length: 2, nullable: true)]
	private ?string $languageCode;

	#[ORM\Column(name: 'country_code', type: 'string', length: 2, nullable: true)]
	private ?string $countryCode;

	#[ORM\Column(name: 'script', type: 'string', length: 4, nullable: true)]
	private ?string $script;

	#[ORM\Column(name: 'mapping', type: 'text', length: 1000, nullable: true)]
	private ?string $mapping;

	#[ORM\ManyToMany(targetEntity: ContactPerson::class, mappedBy: 'languages', cascade: ['persist'])]
	private mixed $contactPersons;

	#[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'languages', cascade: ['persist'])]
	private mixed $customers;

	public function __construct()
	{
		$this->contactPersons = new ArrayCollection();
		$this->customers      = new ArrayCollection();
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

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

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

	public function getLangiso(): ?string
	{
		return $this->langiso;
	}

	public function setLangiso(string $langiso): self
	{
		$this->langiso = $langiso;

		return $this;
	}

	public function getLangiso3(): ?string
	{
		return $this->langiso3;
	}

	public function setLangiso3(?string $langiso3): self
	{
		$this->langiso3 = $langiso3;

		return $this;
	}

	public function getSymbol(): ?string
	{
		return $this->symbol;
	}

	public function setSymbol(string $symbol): self
	{
		$this->symbol = $symbol;

		return $this;
	}

	public function getMultitermAlias(): ?string
	{
		return $this->multitermAlias;
	}

	public function setMultitermAlias(?string $multitermAlias): self
	{
		$this->multitermAlias = $multitermAlias;

		return $this;
	}

	public function getLanguageCode(): ?string
	{
		return $this->languageCode;
	}

	public function setLanguageCode(?string $languageCode): self
	{
		$this->languageCode = $languageCode;

		return $this;
	}

	public function getScript(): ?string
	{
		return $this->script;
	}

	public function setScript(?string $script): self
	{
		$this->script = $script;

		return $this;
	}

	public function getContactPersons(): Collection
	{
		return $this->contactPersons;
	}

	public function addContactPerson(ContactPerson $contactPerson): self
	{
		if (!$this->contactPersons->contains($contactPerson)) {
			$this->contactPersons[] = $contactPerson;
			$contactPerson->addLanguage($this);
		}

		return $this;
	}

	public function removeContactPerson(ContactPerson $contactPerson): self
	{
		if ($this->contactPersons->contains($contactPerson)) {
			$this->contactPersons->removeElement($contactPerson);
			$contactPerson->removeLanguage($this);
		}

		return $this;
	}

	public function getCustomers(): Collection
	{
		return $this->customers;
	}

	public function addCustomer(Customer $customer): self
	{
		if (!$this->customers->contains($customer)) {
			$this->customers[] = $customer;
			$customer->addLanguage($this);
		}

		return $this;
	}

	public function removeCustomer(Customer $customer): self
	{
		if ($this->customers->contains($customer)) {
			$this->customers->removeElement($customer);
			$customer->removeLanguage($this);
		}

		return $this;
	}

	public function getCountryCode(): ?string
	{
		return $this->countryCode;
	}

	public function setCountryCode(?string $countryCode): self
	{
		$this->countryCode = $countryCode;

		return $this;
	}

	public function getMapping(): ?string
	{
		return $this->mapping;
	}

	public function setMapping(?string $mapping): self
	{
		$this->mapping = $mapping;

		return $this;
	}
}
