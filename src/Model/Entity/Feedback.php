<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'feedback')]
#[ORM\Entity]
class Feedback implements EntityInterface
{
	public const TYPE_CLIENT_COMPLIANT       = 'CUSTOMER_CLAIM';
	public const TYPE_CLIENT_APPROVAL        = 'CUSTOMER_APPROVAL';
	public const TYPE_INTERNAL_NONCONFORMITY = 'NON_CONFORMITY';

	public const STATUS_GROUNDLESS = 'GROUNDLESS';
	public const STATUS_OPEN       = 'OPENED';
	public const STATUS_RESOLVED   = 'RESOLVED';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'feedback_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'feedback_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'cause_of_discrepancy', type: 'text', nullable: true)]
	private ?string $causeOfDiscrepancy;

	#[ORM\Column(name: 'feedback_type', type: 'string', nullable: true)]
	private ?string $feedbackType;

	#[ORM\Column(name: 'corrective_and_preventive_actions', type: 'text', nullable: true)]
	private ?string $correctiveAndPreventiveActions;

	#[ORM\Column(name: 'creation_date', type: 'datetime', nullable: false)]
	private \DateTimeInterface $creationDate;

	#[ORM\Column(name: 'deadline_for_implementation', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $deadlineForImplementation;

	#[ORM\Column(name: 'description_of_claim', type: 'text', nullable: true)]
	private ?string $descriptionOfClaim;

	#[ORM\Column(name: 'efficiency_approved_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $efficiencyApprovedDate;

	#[ORM\Column(name: 'id_number', type: 'string', nullable: false)]
	private string $idNumber;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $createdByUser;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'efficiency_approved_by_user_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $efficiencyApprovedByUser;

	#[ORM\Column(name: 'related_activity_id', type: 'bigint', nullable: true)]
	private ?string $relatedActivity;

	#[ORM\Column(name: 'template_id', type: 'bigint', nullable: true)]
	private ?string $template;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'related_project_id', referencedColumnName: 'project_id', nullable: true)]
	private ?Project $relatedProject;

	#[ORM\JoinTable(name: 'feedback_related_tasks')]
	#[ORM\JoinColumn(name: 'feedback_id', referencedColumnName: 'feedback_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'task_id', referencedColumnName: 'task_id')]
	#[ORM\ManyToMany(targetEntity: Task::class, cascade: ['persist'], inversedBy: 'feedbacks')]
	protected mixed $tasks;

	#[ORM\JoinTable(name: 'feedback_related_users')]
	#[ORM\JoinColumn(name: 'feedback_id', referencedColumnName: 'feedback_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'xtrf_user_id')]
	#[ORM\ManyToMany(targetEntity: User::class, cascade: ['persist'], inversedBy: 'feedbacks')]
	protected mixed $users;

	#[ORM\JoinTable(name: 'feedback_responsible_for_implementation')]
	#[ORM\JoinColumn(name: 'feedback_id', referencedColumnName: 'feedback_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'xtrf_user_id')]
	#[ORM\ManyToMany(targetEntity: User::class, cascade: ['persist'], inversedBy: 'responsibleFeedbacks')]
	protected mixed $responsibleUsers;

	#[ORM\JoinTable(name: 'feedback_related_providers')]
	#[ORM\JoinColumn(name: 'feedback_id', referencedColumnName: 'feedback_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'provider_id', referencedColumnName: 'provider_id')]
	#[ORM\ManyToMany(targetEntity: Provider::class, cascade: ['persist'], inversedBy: 'feedbacks')]
	protected mixed $providers;

	public function __construct()
	{
		$this->tasks            = new ArrayCollection();
		$this->users            = new ArrayCollection();
		$this->responsibleUsers = new ArrayCollection();
		$this->providers        = new ArrayCollection();
	}

	/**
	 * @return mixed
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getCauseOfDiscrepancy(): ?string
	{
		return $this->causeOfDiscrepancy;
	}

	/**
	 * @return mixed
	 */
	public function setCauseOfDiscrepancy(?string $causeOfDiscrepancy): self
	{
		$this->causeOfDiscrepancy = $causeOfDiscrepancy;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFeedbackType(): ?string
	{
		return $this->feedbackType;
	}

	/**
	 * @return mixed
	 */
	public function setFeedbackType(?string $feedbackType): self
	{
		$this->feedbackType = $feedbackType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCorrectiveAndPreventiveActions(): ?string
	{
		return $this->correctiveAndPreventiveActions;
	}

	/**
	 * @return mixed
	 */
	public function setCorrectiveAndPreventiveActions(?string $correctiveAndPreventiveActions): self
	{
		$this->correctiveAndPreventiveActions = $correctiveAndPreventiveActions;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreationDate(): ?\DateTimeInterface
	{
		return $this->creationDate;
	}

	/**
	 * @return mixed
	 */
	public function setCreationDate(\DateTimeInterface $creationDate): self
	{
		$this->creationDate = $creationDate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDeadlineForImplementation(): ?\DateTimeInterface
	{
		return $this->deadlineForImplementation;
	}

	/**
	 * @return mixed
	 */
	public function setDeadlineForImplementation(?\DateTimeInterface $deadlineForImplementation): self
	{
		$this->deadlineForImplementation = $deadlineForImplementation;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDescriptionOfClaim(): ?string
	{
		return $this->descriptionOfClaim;
	}

	/**
	 * @return mixed
	 */
	public function setDescriptionOfClaim(?string $descriptionOfClaim): self
	{
		$this->descriptionOfClaim = $descriptionOfClaim;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEfficiencyApprovedDate(): ?\DateTimeInterface
	{
		return $this->efficiencyApprovedDate;
	}

	/**
	 * @return mixed
	 */
	public function setEfficiencyApprovedDate(?\DateTimeInterface $efficiencyApprovedDate): self
	{
		$this->efficiencyApprovedDate = $efficiencyApprovedDate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIdNumber(): ?string
	{
		return $this->idNumber;
	}

	/**
	 * @return mixed
	 */
	public function setIdNumber(string $idNumber): self
	{
		$this->idNumber = $idNumber;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStatus(): ?string
	{
		return $this->status;
	}

	/**
	 * @return mixed
	 */
	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRelatedActivity(): ?string
	{
		return $this->relatedActivity;
	}

	/**
	 * @return mixed
	 */
	public function setRelatedActivity(?string $relatedActivity): self
	{
		$this->relatedActivity = $relatedActivity;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTemplate(): ?string
	{
		return $this->template;
	}

	/**
	 * @return mixed
	 */
	public function setTemplate(?string $template): self
	{
		$this->template = $template;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedByUser(): ?User
	{
		return $this->createdByUser;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedByUser(?User $createdByUser): self
	{
		$this->createdByUser = $createdByUser;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEfficiencyApprovedByUser(): ?User
	{
		return $this->efficiencyApprovedByUser;
	}

	/**
	 * @return mixed
	 */
	public function setEfficiencyApprovedByUser(?User $efficiencyApprovedByUser): self
	{
		$this->efficiencyApprovedByUser = $efficiencyApprovedByUser;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getRelatedProject(): ?Project
	{
		return $this->relatedProject;
	}

	/**
	 * @return mixed
	 */
	public function setRelatedProject(?Project $relatedProject): self
	{
		$this->relatedProject = $relatedProject;

		return $this;
	}

	/**
	 * @return Collection|Task[]
	 */
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
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeTask(Task $task): self
	{
		$this->tasks->removeElement($task);

		return $this;
	}

	/**
	 * @return Collection|User[]
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	/**
	 * @return mixed
	 */
	public function addUser(User $user): self
	{
		if (!$this->users->contains($user)) {
			$this->users[] = $user;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeUser(User $user): self
	{
		$this->users->removeElement($user);

		return $this;
	}

	/**
	 * @return Collection|User[]
	 */
	public function getResponsibleUsers(): Collection
	{
		return $this->responsibleUsers;
	}

	/**
	 * @return mixed
	 */
	public function addResponsibleUser(User $responsibleUser): self
	{
		if (!$this->responsibleUsers->contains($responsibleUser)) {
			$this->responsibleUsers[] = $responsibleUser;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeResponsibleUser(User $responsibleUser): self
	{
		$this->responsibleUsers->removeElement($responsibleUser);

		return $this;
	}

	/**
	 * @return Collection|Provider[]
	 */
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
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeProvider(Provider $provider): self
	{
		$this->providers->removeElement($provider);

		return $this;
	}
}
