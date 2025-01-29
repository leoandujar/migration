<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'language_specialization')]
#[ORM\UniqueConstraint(name: 'language_specialization_name_key', columns: ['name'])]
#[ORM\Entity]
class LanguageSpecialization implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'language_specialization_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'language_specialization_id', type: 'bigint')]
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

	#[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'languageSpecializations', cascade: ['persist'])]
	private mixed $customers;

	#[ORM\ManyToMany(targetEntity: CustomerLanguageCombination::class, mappedBy: 'languagesSpecialization', cascade: ['persist'])]
	private mixed $customerLanguageCombinations;

	public function __construct()
	{
		$this->customers                    = new ArrayCollection();
		$this->customerLanguageCombinations = new ArrayCollection();
	}

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

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	/**
	 * @return mixed
	 */
	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	public function getDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultEntity(bool $defaultEntity): self
	{
		$this->defaultEntity = $defaultEntity;

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

	public function getPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	/**
	 * @return mixed
	 */
	public function setPreferedEntity(bool $preferedEntity): self
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	public function getCustomers(): Collection
	{
		return $this->customers;
	}

	/**
	 * @return mixed
	 */
	public function addCustomer(Customer $customer): self
	{
		if (!$this->customers->contains($customer)) {
			$this->customers[] = $customer;
			$customer->addLanguageSpecialization($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomer(Customer $customer): self
	{
		if ($this->customers->contains($customer)) {
			$this->customers->removeElement($customer);
			$customer->removeLanguageSpecialization($this);
		}

		return $this;
	}

	public function getCustomerLanguageCombinations(): Collection
	{
		return $this->customerLanguageCombinations;
	}

	/**
	 * @return mixed
	 */
	public function addCustomerLanguageCombination(CustomerLanguageCombination $customerLanguageCombination): self
	{
		if (!$this->customerLanguageCombinations->contains($customerLanguageCombination)) {
			$this->customerLanguageCombinations[] = $customerLanguageCombination;
			$customerLanguageCombination->addLanguagesSpecialization($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomerLanguageCombination(CustomerLanguageCombination $customerLanguageCombination): self
	{
		if ($this->customerLanguageCombinations->contains($customerLanguageCombination)) {
			$this->customerLanguageCombinations->removeElement($customerLanguageCombination);
			$customerLanguageCombination->removeLanguagesSpecialization($this);
		}

		return $this;
	}
}
