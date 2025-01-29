<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'category')]
#[ORM\UniqueConstraint(name: 'category_name_key', columns: ['name'])]
#[ORM\Entity]
class Category implements EntityInterface
{
	public const CATEGORY_LQA = 'LQA';
	public const CATEGORY_MT = 'MT';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'category_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'category_id', type: 'bigint')]
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

	#[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $tasks;

	#[ORM\ManyToMany(targetEntity: Quote::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $quotes;

	#[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $projects;

	#[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $customers;

	#[ORM\ManyToMany(targetEntity: ContactPerson::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $contactPersons;

	#[ORM\ManyToMany(targetEntity: CustomerInvoice::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $customerInvoices;

	#[ORM\ManyToMany(targetEntity: Provider::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $providers;

	#[ORM\ManyToMany(targetEntity: ProviderInvoice::class, mappedBy: 'categories', cascade: ['persist'])]
	private mixed $providersInvoice;

	public function __construct()
	{
		$this->tasks            = new ArrayCollection();
		$this->quotes           = new ArrayCollection();
		$this->projects         = new ArrayCollection();
		$this->customers        = new ArrayCollection();
		$this->contactPersons   = new ArrayCollection();
		$this->customerInvoices = new ArrayCollection();
		$this->providers        = new ArrayCollection();
		$this->providersInvoice = new ArrayCollection();
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

	public function getTasks(): Collection
	{
		return $this->tasks;
	}

	/**
	 * @return mixed
	 */
	public function addTask(Task $task): self
	{
		if (!$this->tasks->contains($task)) {
			$this->tasks[] = $task;
			$task->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeTask(Task $task): self
	{
		if ($this->tasks->contains($task)) {
			$this->tasks->removeElement($task);
			$task->removeCategory($this);
		}

		return $this;
	}

	public function getQuotes(): Collection
	{
		return $this->quotes;
	}

	/**
	 * @return mixed
	 */
	public function addQuote(Quote $quote): self
	{
		if (!$this->quotes->contains($quote)) {
			$this->quotes[] = $quote;
			$quote->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeQuote(Quote $quote): self
	{
		if ($this->quotes->contains($quote)) {
			$this->quotes->removeElement($quote);
			$quote->removeCategory($this);
		}

		return $this;
	}

	public function getProjects(): Collection
	{
		return $this->projects;
	}

	/**
	 * @return mixed
	 */
	public function addProject(Project $project): self
	{
		if (!$this->projects->contains($project)) {
			$this->projects[] = $project;
			$project->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProject(Project $project): self
	{
		if ($this->projects->contains($project)) {
			$this->projects->removeElement($project);
			$project->removeCategory($this);
		}

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
			$customer->addCategory($this);
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
			$customer->removeCategory($this);
		}

		return $this;
	}

	public function getContactPersons(): Collection
	{
		return $this->contactPersons;
	}

	/**
	 * @return mixed
	 */
	public function addContactPerson(ContactPerson $contactPerson): self
	{
		if (!$this->contactPersons->contains($contactPerson)) {
			$this->contactPersons[] = $contactPerson;
			$contactPerson->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeContactPerson(ContactPerson $contactPerson): self
	{
		if ($this->contactPersons->contains($contactPerson)) {
			$this->contactPersons->removeElement($contactPerson);
			$contactPerson->removeCategory($this);
		}

		return $this;
	}

	public function getCustomerInvoices(): Collection
	{
		return $this->customerInvoices;
	}

	/**
	 * @return mixed
	 */
	public function addCustomerInvoice(CustomerInvoice $customerInvoice): self
	{
		if (!$this->customerInvoices->contains($customerInvoice)) {
			$this->customerInvoices[] = $customerInvoice;
			$customerInvoice->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomerInvoice(CustomerInvoice $customerInvoice): self
	{
		if ($this->customerInvoices->contains($customerInvoice)) {
			$this->customerInvoices->removeElement($customerInvoice);
			$customerInvoice->removeCategory($this);
		}

		return $this;
	}

	public function getProviders(): Collection
	{
		return $this->providers;
	}

	/**
	 * @return mixed
	 */
	public function addProvider(Provider $provider): self
	{
		if (!$this->providers->contains($provider)) {
			$this->providers[] = $provider;
			$provider->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProvider(Provider $provider): self
	{
		if ($this->providers->contains($provider)) {
			$this->providers->removeElement($provider);
			$provider->removeCategory($this);
		}

		return $this;
	}

	public function getProvidersInvoice(): Collection
	{
		return $this->providersInvoice;
	}

	/**
	 * @return mixed
	 */
	public function addProvidersInvoice(ProviderInvoice $providersInvoice): self
	{
		if (!$this->providersInvoice->contains($providersInvoice)) {
			$this->providersInvoice[] = $providersInvoice;
			$providersInvoice->addCategory($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProvidersInvoice(ProviderInvoice $providersInvoice): self
	{
		if ($this->providersInvoice->contains($providersInvoice)) {
			$this->providersInvoice->removeElement($providersInvoice);
			$providersInvoice->removeCategory($this);
		}

		return $this;
	}
}
