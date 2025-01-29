<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'workflow_job_file')]
#[ORM\Entity]
class WorkflowJobFile implements EntityInterface
{
	public const CATEGORY_WORKFILE = 'WORKFILE';
	public const CATEGORY_REF      = 'REF';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'workflow_job_file_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'workflow_job_file_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'base_dir', type: 'text', nullable: true)]
	private ?string $baseDir;

	#[ORM\Column(name: 'category', type: 'string', nullable: true)]
	private ?string $category;

	#[ORM\Column(name: 'original_file_id', type: 'string', nullable: true)]
	private ?string $originalFileId;

	#[ORM\Column(name: 'url', type: 'string', nullable: true)]
	private ?string $url;

	#[ORM\Column(name: 'include_in_availability_request', type: 'boolean', nullable: false)]
	private bool $includeInAvailabilityRequest;

	#[ORM\Column(name: 'name', type: 'text', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'provider_time_spent', type: 'integer', nullable: true)]
	private ?int $providerTimeSpent;

	#[ORM\Column(name: 'relative_dir', type: 'text', nullable: true)]
	private ?string $relativeDir;

	#[ORM\Column(name: 'resource_id', type: 'string', nullable: true)]
	private ?string $resourceId;

	#[ORM\Column(name: 'resource_type', type: 'string', nullable: true)]
	private ?string $resourceType;

	#[ORM\ManyToOne(targetEntity: Activity::class)]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', nullable: true)]
	private ?Activity $activity;

	#[ORM\Column(name: 'linked_workflow_job_file_id', type: 'bigint', nullable: true)]
	private ?string $linkedWorkflowJobFileId;

	#[ORM\Column(name: 'loose_bundle_id', type: 'bigint', nullable: true)]
	private ?string $looseBundleId;

	#[ORM\Column(name: 'external_system_id', type: 'bigint', nullable: true)]
	private ?string $externalSystemId;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'workflowJobFiles')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', nullable: true)]
	private ?Task $task;

	#[ORM\Column(name: 'task_output_id', type: 'bigint', nullable: true)]
	private ?string $taskOutputId;

	#[ORM\Column(name: 'file_stats_status', type: 'text', nullable: false)]
	private string $fileStatsStatus;

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

	public function getBaseDir(): ?string
	{
		return $this->baseDir;
	}

	public function setBaseDir(?string $baseDir): self
	{
		$this->baseDir = $baseDir;

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

	public function getOriginalFileId(): ?string
	{
		return $this->originalFileId;
	}

	public function setOriginalFileId(?string $originalFileId): self
	{
		$this->originalFileId = $originalFileId;

		return $this;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function setUrl(?string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public function getIncludeInAvailabilityRequest(): ?bool
	{
		return $this->includeInAvailabilityRequest;
	}

	public function setIncludeInAvailabilityRequest(bool $includeInAvailabilityRequest): self
	{
		$this->includeInAvailabilityRequest = $includeInAvailabilityRequest;

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

	public function getProviderTimeSpent(): ?int
	{
		return $this->providerTimeSpent;
	}

	public function setProviderTimeSpent(?int $providerTimeSpent): self
	{
		$this->providerTimeSpent = $providerTimeSpent;

		return $this;
	}

	public function getRelativeDir(): ?string
	{
		return $this->relativeDir;
	}

	public function setRelativeDir(?string $relativeDir): self
	{
		$this->relativeDir = $relativeDir;

		return $this;
	}

	public function getResourceId(): ?string
	{
		return $this->resourceId;
	}

	public function setResourceId(?string $resourceId): self
	{
		$this->resourceId = $resourceId;

		return $this;
	}

	public function getResourceType(): ?string
	{
		return $this->resourceType;
	}

	public function setResourceType(?string $resourceType): self
	{
		$this->resourceType = $resourceType;

		return $this;
	}

	public function getLinkedWorkflowJobFileId(): ?string
	{
		return $this->linkedWorkflowJobFileId;
	}

	public function setLinkedWorkflowJobFileId(?string $linkedWorkflowJobFileId): self
	{
		$this->linkedWorkflowJobFileId = $linkedWorkflowJobFileId;

		return $this;
	}

	public function getLooseBundleId(): ?string
	{
		return $this->looseBundleId;
	}

	public function setLooseBundleId(?string $looseBundleId): self
	{
		$this->looseBundleId = $looseBundleId;

		return $this;
	}

	public function getExternalSystemId(): ?string
	{
		return $this->externalSystemId;
	}

	public function setExternalSystemId(?string $externalSystemId): self
	{
		$this->externalSystemId = $externalSystemId;

		return $this;
	}

	public function getTaskOutputId(): ?string
	{
		return $this->taskOutputId;
	}

	public function setTaskOutputId(?string $taskOutputId): self
	{
		$this->taskOutputId = $taskOutputId;

		return $this;
	}

	public function getFileStatsStatus(): ?string
	{
		return $this->fileStatsStatus;
	}

	public function setFileStatsStatus(string $fileStatsStatus): self
	{
		$this->fileStatsStatus = $fileStatsStatus;

		return $this;
	}

	public function getActivity(): ?Activity
	{
		return $this->activity;
	}

	public function setActivity(?Activity $activity): self
	{
		$this->activity = $activity;

		return $this;
	}

	public function getTask(): ?Task
	{
		return $this->task;
	}

	public function setTask(?Task $task): self
	{
		$this->task = $task;

		return $this;
	}
}
