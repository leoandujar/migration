<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'project_open_statistics')]
#[ORM\Entity]
class TimeSerieStats
{
	#[ORM\Id]
	#[ORM\Column(name: 'time', type: 'string', nullable: false)]
	private string $time;

	/**
	 * @var Customer|null
	 */
	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	/**
	 * @var User|null
	 */
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'project_manager_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $projectManager;

	/**
	 * @var User|null
	 */
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'project_coordinator_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $coordinatorManager;

	#[ORM\Column(name: 'open_projects', type: 'integer', nullable: false)]
	private int $openProjects;

	#[ORM\Column(name: 'open_tasks', type: 'integer', nullable: false)]
	private int $openTasks;

	#[ORM\Column(name: 'requested_quotes', type: 'integer', nullable: false)]
	private int $requestedQuotes;

	/**
	 * @var float
	 */
	#[ORM\Column(name: 'total_agreed', type: 'decimal', precision: 19, scale: 6, nullable: false)]
	private float $totalAgreed;

	/**
	 * @var float
	 */
	#[ORM\Column(name: 'total_cost', type: 'decimal', precision: 19, scale: 6, nullable: false)]
	private float $totalCost;

	#[ORM\Column(name: 'total_words', type: 'integer', nullable: false)]
	private int $totalWords;

	#[ORM\Column(name: 'total_working_files', type: 'integer', nullable: false)]
	private int $totalWorkingFiles;

	#[ORM\Column(name: 'coordinator_tasks', type: 'integer', nullable: false)]
	private int $coordinatorTasks;

	/**
	 * @return mixed
	 */
	public function getTime(): string
	{
		return $this->time;
	}

	/**
	 * @return mixed
	 */
	public function getOpenProjects(): ?int
	{
		return $this->openProjects;
	}

	/**
	 * @return mixed
	 */
	public function setTime(string $time): self
	{
		$this->time = $time;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function setOpenProjects(int $openProjects): self
	{
		$this->openProjects = $openProjects;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOpenTasks(): ?int
	{
		return $this->openTasks;
	}

	/**
	 * @return mixed
	 */
	public function setOpenTasks(int $openTasks): self
	{
		$this->openTasks = $openTasks;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRequestedQuotes(): ?int
	{
		return $this->requestedQuotes;
	}

	/**
	 * @return mixed
	 */
	public function setRequestedQuotes(int $requestedQuotes): self
	{
		$this->requestedQuotes = $requestedQuotes;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTotalAgreed(): ?string
	{
		return $this->totalAgreed;
	}

	/**
	 * @return mixed
	 */
	public function setTotalAgreed(string $totalAgreed): self
	{
		$this->totalAgreed = $totalAgreed;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTotalCost(): ?string
	{
		return $this->totalCost;
	}

	/**
	 * @return mixed
	 */
	public function setTotalCost(string $totalCost): self
	{
		$this->totalCost = $totalCost;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTotalWords(): ?int
	{
		return $this->totalWords;
	}

	/**
	 * @return mixed
	 */
	public function setTotalWords(int $totalWords): self
	{
		$this->totalWords = $totalWords;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTotalWorkingFiles(): ?int
	{
		return $this->totalWorkingFiles;
	}

	/**
	 * @return mixed
	 */
	public function setTotalWorkingFiles(int $totalWorkingFiles): self
	{
		$this->totalWorkingFiles = $totalWorkingFiles;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCoordinatorTasks(): ?int
	{
		return $this->coordinatorTasks;
	}

	/**
	 * @return mixed
	 */
	public function setCoordinatorTasks(int $coordinatorTasks): self
	{
		$this->coordinatorTasks = $coordinatorTasks;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getProjectManager(): ?User
	{
		return $this->projectManager;
	}

	/**
	 * @return mixed
	 */
	public function setProjectManager(?User $projectManager): self
	{
		$this->projectManager = $projectManager;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCoordinatorManager(): ?User
	{
		return $this->coordinatorManager;
	}

	/**
	 * @return mixed
	 */
	public function setCoordinatorManager(?User $coordinatorManager): self
	{
		$this->coordinatorManager = $coordinatorManager;

		return $this;
	}
}
