<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_workflow_monitor')]
#[ORM\Index(name: '', columns: ['av_workflow_monitor_id'])]
#[ORM\Entity]
class AVWorkflowMonitor implements EntityInterface
{
	public const STATUS_PENDING = 1;
	public const STATUS_RUNNING = 2;
	public const STATUS_FINISHED = 3;
	public const STATUS_FAILED = -1;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'av_workflow_monitor_id_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'av_workflow_monitor_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'workflowMonitors')]
	#[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $createdBy;

	#[ORM\ManyToOne(targetEntity: WFWorkflow::class, inversedBy: 'workflowMonitors')]
	#[ORM\JoinColumn(name: 'workflow', referencedColumnName: 'wf_workflow_id', nullable: false)]
	private WFWorkflow $workflow;

	#[ORM\Column(name: 'status', type: 'integer', nullable: false)]
	private int $status = self::STATUS_PENDING;

	#[ORM\Column(name: 'ordered_at', type: 'datetime', nullable: false)]
	private \DateTimeInterface $orderedAt;

	#[ORM\Column(name: 'started_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $startedAt;

	#[ORM\Column(name: 'finished_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $finishedAt;

	#[ORM\Column(name: 'details', type: 'json', nullable: true)]
	private ?array $details = [];

	#[ORM\Column(name: 'auxiliary_data', type: 'json', nullable: true)]
	private ?array $auxiliaryData = [];

	/**
	 * APForm constructor.
	 */
	public function __construct()
	{
		$this->orderedAt = new \DateTime('now');
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getStatus(): ?int
	{
		return $this->status;
	}

	public function setStatus(int $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getOrderedAt(): ?\DateTimeInterface
	{
		return $this->orderedAt;
	}

	public function setOrderedAt(\DateTimeInterface $orderedAt): self
	{
		$this->orderedAt = $orderedAt;

		return $this;
	}

	public function getStartedAt(): ?\DateTimeInterface
	{
		return $this->startedAt;
	}

	public function setStartedAt(?\DateTimeInterface $startedAt): self
	{
		$this->startedAt = $startedAt;

		return $this;
	}

	public function getFinishedAt(): ?\DateTimeInterface
	{
		return $this->finishedAt;
	}

	public function setFinishedAt(?\DateTimeInterface $finishedAt): self
	{
		$this->finishedAt = $finishedAt;

		return $this;
	}

	public function getDetails(): ?array
	{
		return $this->details;
	}

	public function setDetails(?array $details): self
	{
		$this->details = $details;

		return $this;
	}

	public function getCreatedBy(): ?InternalUser
	{
		return $this->createdBy;
	}

	public function setCreatedBy(?InternalUser $createdBy): self
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	public function getWorkflow(): ?WFWorkflow
	{
		return $this->workflow;
	}

	public function setWorkflow(?WFWorkflow $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}

	public function getAuxiliaryData(): array
	{
		return $this->auxiliaryData;
	}

	public function setAuxiliaryData(?array $auxiliaryData): self
	{
		$this->auxiliaryData = $auxiliaryData;

		return $this;
	}
}
