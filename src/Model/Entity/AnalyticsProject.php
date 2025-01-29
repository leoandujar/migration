<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'xtm_analytics_project')]
#[ORM\Index(name: '', columns: ['activity_id'])]
#[ORM\UniqueConstraint(name: 'extid_lang_idx', columns: ['external_id', 'xtrf_language_id'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\AnalyticsProjectRepository')]
class AnalyticsProject implements EntityInterface
{
	public const CREATED = 0;
    public const TEST_DELETED = 10;
	public const LINKED = 1;
	public const JOBS_PROCESSED = 2;
	public const METRICS_PROCESSED = 3;
	public const STATISTICS_PROCESSED = 4;
	public const EXTENDED_TABLE_PROCESSED = 5;

	public const S_NOT_STARTED = 'NOT_STARTED';
	public const S_STARTED = 'STARTED';
	public const S_FINISHED = 'FINISHED';

	public const A_ACTIVE = 'ACTIVE';
	public const A_ARCHIVED = 'ARCHIVED';
	public const A_AUTO_ACTIVE = 'AUTO_ARCHIVED';
	public const A_DELETED = 'DELETED';
	public const A_INACTIVE = 'INACTIVE';

	public const ED_NOT_STARTED = 0;
	public const ED_SKIPPED = 2;
	public const ED_FINISHED = 3;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'analytic_project_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'external_id', type: 'integer')]
	private int $externalId;

	#[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'analyticsProjects')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', nullable: true)]
	private ?Project $project;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'analyticsProjects')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', nullable: true)]
	private ?Task $task;

	#[ORM\ManyToOne(targetEntity: Activity::class, cascade: ['persist'], inversedBy: 'analyticsProjects')]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', nullable: true, unique: false)]
	private ?Activity $job;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'project_human_id', type: 'string', length: 255, nullable: true)]
	private ?string $projectHumanId;

	#[ORM\Column(name: 'create_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createDate;

	#[ORM\Column(name: 'start_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $startDate;

	#[ORM\Column(name: 'finish_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $finishDate;

	#[ORM\Column(name: 'due_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dueDate;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'xtrf_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguageTag;

	#[ORM\ManyToOne(targetEntity: Language::class)]
	#[ORM\JoinColumn(name: 'language_id', referencedColumnName: 'id', nullable: true)]
	private ?Language $targetLanguage;

	#[ORM\Column(name: 'language_code', type: 'string', length: 255, nullable: true)]
	private ?string $targetLanguageCode;

	#[ORM\Column(name: 'activity', type: 'string', length: 50, nullable: true)]
	private ?string $activity;

	#[ORM\Column(name: 'status', type: 'string', length: 50, nullable: false)]
	private string $status;

	#[ORM\Column(name: 'processing_status', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $processingStatus = self::CREATED;

	#[ORM\Column(name: 'ignored', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $ignored = false;

	#[ORM\Column(name: 'lqa_file_id', type: 'bigint', nullable: true)]
	private ?string $lqaFileId;

	#[ORM\Column(name: 'extended_table_file_id', type: 'bigint', nullable: true)]
	private ?string $extendedTableFileId;

	#[ORM\Column(name: 'lqa_allowed', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $lqaAllowed = false;

	#[ORM\Column(name: 'edit_distance_allowed', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $editDistanceAllowed = false;

	#[ORM\Column(name: 'edit_distance_status', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $editDistanceStatus = 0;

	#[ORM\Column(name: 'lqa_processed', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $lqaProcessed = false;

	#[ORM\Column(name: 'fetch_attempts', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $fetchAttempts = 0;

	#[ORM\OneToMany(targetEntity: AnalyticsProjectStep::class, mappedBy: 'analyticsProject', orphanRemoval: true)]
	private mixed $analyticsProjectSteps;

	#[ORM\OneToOne(targetEntity: XtmEditDistance::class, mappedBy: 'analyticsProject', orphanRemoval: true)]
	private XtmEditDistance $xtmEditDistance;

	#[ORM\OneToMany(targetEntity: LqaIssue::class, mappedBy: 'analyticsProject', orphanRemoval: true)]
	private mixed $lqaIssues;

	public function __construct()
	{
		$this->analyticsProjectSteps = new ArrayCollection();
		$this->lqaIssues = new ArrayCollection();
	}

	public function __set($property, $value): void
	{
		$this->$property = $value;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getExternalId(): ?int
	{
		return $this->externalId;
	}

	/**
	 * @return mixed
	 */
	public function setExternalId(int $externalId): self
	{
		$this->externalId = $externalId;

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

	public function getProjectHumanId(): ?string
	{
		return $this->projectHumanId;
	}

	/**
	 * @return mixed
	 */
	public function setProjectHumanId(?string $projectHumanId): self
	{
		$this->projectHumanId = $projectHumanId;

		return $this;
	}

	public function getCreateDate(): ?\DateTimeInterface
	{
		return $this->createDate;
	}

	/**
	 * @return mixed
	 */
	public function setCreateDate(?\DateTimeInterface $createDate): self
	{
		$this->createDate = $createDate;

		return $this;
	}

	public function getStartDate(): ?\DateTimeInterface
	{
		return $this->startDate;
	}

	/**
	 * @return mixed
	 */
	public function setStartDate(?\DateTimeInterface $startDate): self
	{
		$this->startDate = $startDate;

		return $this;
	}

	public function getFinishDate(): ?\DateTimeInterface
	{
		return $this->finishDate;
	}

	/**
	 * @return mixed
	 */
	public function setFinishDate(?\DateTimeInterface $finishDate): self
	{
		$this->finishDate = $finishDate;

		return $this;
	}

	public function getDueDate(): ?\DateTimeInterface
	{
		return $this->dueDate;
	}

	/**
	 * @return mixed
	 */
	public function setDueDate(?\DateTimeInterface $dueDate): self
	{
		$this->dueDate = $dueDate;

		return $this;
	}

	public function getTargetLanguageCode(): ?string
	{
		return $this->targetLanguageCode;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguageCode(?string $targetLanguageCode): self
	{
		$this->targetLanguageCode = $targetLanguageCode;

		return $this;
	}

	public function getActivity(): ?string
	{
		return $this->activity;
	}

	/**
	 * @return mixed
	 */
	public function setActivity(string $activity): self
	{
		$this->activity = $activity;

		return $this;
	}

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

	public function getProcessingStatus(): ?int
	{
		return $this->processingStatus;
	}

	/**
	 * @return mixed
	 */
	public function setProcessingStatus(int $processingStatus): self
	{
		$this->processingStatus = $processingStatus;

		return $this;
	}

	public function getIgnored(): ?bool
	{
		return $this->ignored;
	}

	/**
	 * @return mixed
	 */
	public function setIgnored(bool $ignored): self
	{
		$this->ignored = $ignored;

		return $this;
	}

	public function getLqaFileId(): ?string
	{
		return $this->lqaFileId;
	}

	/**
	 * @return mixed
	 */
	public function setLqaFileId(?string $lqaFileId): self
	{
		$this->lqaFileId = $lqaFileId;

		return $this;
	}

	public function getLqaAllowed(): ?bool
	{
		return $this->lqaAllowed;
	}

	/**
	 * @return mixed
	 */
	public function setLqaAllowed(bool $lqaAllowed): self
	{
		$this->lqaAllowed = $lqaAllowed;

		return $this;
	}

	public function getLqaProcessed(): ?bool
	{
		return $this->lqaProcessed;
	}

	/**
	 * @return mixed
	 */
	public function setLqaProcessed(bool $lqaProcessed): self
	{
		$this->lqaProcessed = $lqaProcessed;

		return $this;
	}

	public function getFetchAttempts(): ?int
	{
		return $this->fetchAttempts;
	}

	/**
	 * @return mixed
	 */
	public function setFetchAttempts(int $fetchAttempts): self
	{
		$this->fetchAttempts = $fetchAttempts;

		return $this;
	}

	public function getProject(): ?Project
	{
		return $this->project;
	}

	/**
	 * @return mixed
	 */
	public function setProject(?Project $project): self
	{
		$this->project = $project;

		return $this;
	}

	public function getTask(): ?Task
	{
		return $this->task;
	}

	/**
	 * @return mixed
	 */
	public function setTask(?Task $task): self
	{
		$this->task = $task;

		return $this;
	}

	public function getTargetLanguageTag(): ?XtrfLanguage
	{
		return $this->targetLanguageTag;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguageTag(?XtrfLanguage $targetLanguageTag): self
	{
		$this->targetLanguageTag = $targetLanguageTag;

		return $this;
	}

	public function getTargetLanguage(): ?Language
	{
		return $this->targetLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguage(?Language $targetLanguage): self
	{
		$this->targetLanguage = $targetLanguage;

		return $this;
	}

	public function getAnalyticsProjectSteps(): Collection
	{
		return $this->analyticsProjectSteps;
	}

	/**
	 * @return mixed
	 */
	public function addAnalyticsProjectStep(AnalyticsProjectStep $analyticsProjectStep): self
	{
		if (!$this->analyticsProjectSteps->contains($analyticsProjectStep)) {
			$this->analyticsProjectSteps[] = $analyticsProjectStep;
			$analyticsProjectStep->setAnalyticsProject($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeAnalyticsProjectStep(AnalyticsProjectStep $analyticsProjectStep): self
	{
		if ($this->analyticsProjectSteps->contains($analyticsProjectStep)) {
			$this->analyticsProjectSteps->removeElement($analyticsProjectStep);
			// set the owning side to null (unless already changed)
			if ($analyticsProjectStep->getAnalyticsProject() === $this) {
				$analyticsProjectStep->setAnalyticsProject(null);
			}
		}

		return $this;
	}

	public function getLqaIssues(): Collection
	{
		return $this->lqaIssues;
	}

	/**
	 * @return mixed
	 */
	public function addLqaIssue(LqaIssue $lqaIssue): self
	{
		if (!$this->lqaIssues->contains($lqaIssue)) {
			$this->lqaIssues[] = $lqaIssue;
			$lqaIssue->setAnalyticsProject($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLqaIssue(LqaIssue $lqaIssue): self
	{
		if ($this->lqaIssues->contains($lqaIssue)) {
			$this->lqaIssues->removeElement($lqaIssue);
			// set the owning side to null (unless already changed)
			if ($lqaIssue->getAnalyticsProject() === $this) {
				$lqaIssue->setAnalyticsProject(null);
			}
		}

		return $this;
	}

	public function getJob(): ?Activity
	{
		return $this->job;
	}

	/**
	 * @return mixed
	 */
	public function setJob(?Activity $job): self
	{
		$this->job = $job;

		return $this;
	}

	public function getExtendedTableFileId(): ?string
	{
		return $this->extendedTableFileId;
	}

	public function setExtendedTableFileId(?string $extendedTableFileId): self
	{
		$this->extendedTableFileId = $extendedTableFileId;

		return $this;
	}

	public function getXtmEditDistance(): ?XtmEditDistance
	{
		return $this->xtmEditDistance;
	}

	public function setXtmEditDistance(?XtmEditDistance $xtmEditDistance): self
	{
		// unset the owning side of the relation if necessary
		if (null === $xtmEditDistance && null !== $this->xtmEditDistance) {
			$this->xtmEditDistance->setAnalyticsProject(null);
		}

		// set the owning side of the relation if necessary
		if (null !== $xtmEditDistance && $xtmEditDistance->getAnalyticsProject() !== $this) {
			$xtmEditDistance->setAnalyticsProject($this);
		}

		$this->xtmEditDistance = $xtmEditDistance;

		return $this;
	}

	public function getEditDistanceAllowed(): ?bool
	{
		return $this->editDistanceAllowed;
	}

	public function setEditDistanceAllowed(bool $editDistanceAllowed): self
	{
		$this->editDistanceAllowed = $editDistanceAllowed;

		return $this;
	}

	public function getEditDistanceStatus(): ?int
	{
		return $this->editDistanceStatus;
	}

	public function setEditDistanceStatus(int $editDistanceStatus): self
	{
		$this->editDistanceStatus = $editDistanceStatus;

		return $this;
	}
}
