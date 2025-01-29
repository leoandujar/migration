<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'customer_person')]
#[ORM\Entity]
class CustomerPerson implements EntityInterface
{
	#[ORM\Id]
	#[ORM\OneToOne(targetEntity: ContactPerson::class, inversedBy: 'customersPerson')]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: false)]
	private ContactPerson $contactPerson;

	#[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'contactPersons')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\Column(name: 'preferences_id', type: 'bigint', nullable: true)]
	private ?string $preferences;

	#[ORM\Column(name: 'customer_person_salesforce_id', type: 'bigint', nullable: true)]
	private ?string $customerPersonSalesforceId;

	#[ORM\Column(name: 'first_project_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstProjectDate;

	#[ORM\Column(name: 'first_project_date_auto', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $firstProjectDateAuto;

	#[ORM\Column(name: 'first_quote_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstQuoteDate;

	#[ORM\Column(name: 'first_quote_date_auto', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $firstQuoteDateAuto;

	#[ORM\Column(name: 'last_project_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastProjectDate;

	#[ORM\Column(name: 'last_quote_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastQuoteDate;

	#[ORM\Column(name: 'number_of_projects', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $numberOfProjects;

	#[ORM\Column(name: 'number_of_quotes', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $numberOfQuotes;

	#[ORM\ManyToOne(targetEntity: XtrfUserGroup::class)]
	#[ORM\JoinColumn(name: 'xtrf_user_group_id', referencedColumnName: 'xtrf_user_group_id', nullable: true)]
	private ?XtrfUserGroup $userGroup;

	#[ORM\JoinTable(name: 'customer_customer_persons')]
	#[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'contact_person_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'customer_id', referencedColumnName: 'customer_id')]
	#[ORM\ManyToMany(targetEntity: Customer::class, cascade: ['persist'], inversedBy: 'customerPersons')]
	protected mixed $customers;

	#[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'customerPersons', cascade: ['persist'])]
	private mixed $projects;

	#[ORM\ManyToMany(targetEntity: Quote::class, mappedBy: 'customersPerson', cascade: ['persist'])]
	private mixed $quotes;

	#[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'persons', cascade: ['persist'])]
	private mixed $tasks;

	#[ORM\ManyToMany(targetEntity: CustomerAccountencyContactPerson::class, mappedBy: 'customerPerson', cascade: ['persist'])]
	private mixed $customersAccountecy;

	public function __construct()
	{
		$this->customers           = new ArrayCollection();
		$this->projects            = new ArrayCollection();
		$this->quotes              = new ArrayCollection();
		$this->tasks               = new ArrayCollection();
		$this->customersAccountecy = new ArrayCollection();
	}

	public function getPreferences(): ?string
	{
		return $this->preferences;
	}

	/**
	 * @return mixed
	 */
	public function setPreferences(?string $preferences): self
	{
		$this->preferences = $preferences;

		return $this;
	}

	public function getCustomerPersonSalesforceId(): ?string
	{
		return $this->customerPersonSalesforceId;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerPersonSalesforceId(?string $customerPersonSalesforceId): self
	{
		$this->customerPersonSalesforceId = $customerPersonSalesforceId;

		return $this;
	}

	public function getContactPerson(): ?ContactPerson
	{
		return $this->contactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setContactPerson(ContactPerson $contactPerson): self
	{
		$this->contactPerson = $contactPerson;

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

	public function getUserGroup(): ?XtrfUserGroup
	{
		return $this->userGroup;
	}

	/**
	 * @return mixed
	 */
	public function setUserGroup(?XtrfUserGroup $userGroup): self
	{
		$this->userGroup = $userGroup;

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
			$project->addCustomerPerson($this);
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
			$project->removeCustomerPerson($this);
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
			$quote->addCustomersPerson($this);
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
			$quote->removeCustomersPerson($this);
		}

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
			$task->addPerson($this);
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
			$task->removePerson($this);
		}

		return $this;
	}

	public function getCustomersAccountecy(): Collection
	{
		return $this->customersAccountecy;
	}

	/**
	 * @return mixed
	 */
	public function addCustomersAccountecy(CustomerAccountencyContactPerson $customersAccountecy): self
	{
		if (!$this->customersAccountecy->contains($customersAccountecy)) {
			$this->customersAccountecy[] = $customersAccountecy;
			$customersAccountecy->addCustomerPerson($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeCustomersAccountecy(CustomerAccountencyContactPerson $customersAccountecy): self
	{
		if ($this->customersAccountecy->contains($customersAccountecy)) {
			$this->customersAccountecy->removeElement($customersAccountecy);
			$customersAccountecy->removeCustomerPerson($this);
		}

		return $this;
	}

	public function getFirstProjectDate(): ?\DateTimeInterface
	{
		return $this->firstProjectDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstProjectDate(?\DateTimeInterface $firstProjectDate): self
	{
		$this->firstProjectDate = $firstProjectDate;

		return $this;
	}

	public function getFirstProjectDateAuto(): ?bool
	{
		return $this->firstProjectDateAuto;
	}

	/**
	 * @return mixed
	 */
	public function setFirstProjectDateAuto(bool $firstProjectDateAuto): self
	{
		$this->firstProjectDateAuto = $firstProjectDateAuto;

		return $this;
	}

	public function getFirstQuoteDate(): ?\DateTimeInterface
	{
		return $this->firstQuoteDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstQuoteDate(?\DateTimeInterface $firstQuoteDate): self
	{
		$this->firstQuoteDate = $firstQuoteDate;

		return $this;
	}

	public function getFirstQuoteDateAuto(): ?bool
	{
		return $this->firstQuoteDateAuto;
	}

	/**
	 * @return mixed
	 */
	public function setFirstQuoteDateAuto(bool $firstQuoteDateAuto): self
	{
		$this->firstQuoteDateAuto = $firstQuoteDateAuto;

		return $this;
	}

	public function getLastProjectDate(): ?\DateTimeInterface
	{
		return $this->lastProjectDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastProjectDate(?\DateTimeInterface $lastProjectDate): self
	{
		$this->lastProjectDate = $lastProjectDate;

		return $this;
	}

	public function getLastQuoteDate(): ?\DateTimeInterface
	{
		return $this->lastQuoteDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastQuoteDate(?\DateTimeInterface $lastQuoteDate): self
	{
		$this->lastQuoteDate = $lastQuoteDate;

		return $this;
	}

	public function getNumberOfProjects(): ?int
	{
		return $this->numberOfProjects;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfProjects(int $numberOfProjects): self
	{
		$this->numberOfProjects = $numberOfProjects;

		return $this;
	}

	public function getNumberOfQuotes(): ?int
	{
		return $this->numberOfQuotes;
	}

	/**
	 * @return mixed
	 */
	public function setNumberOfQuotes(int $numberOfQuotes): self
	{
		$this->numberOfQuotes = $numberOfQuotes;

		return $this;
	}
}
