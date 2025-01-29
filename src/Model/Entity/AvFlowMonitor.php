<?php

namespace App\Model\Entity;

use App\Model\Repository\AvFlowMonitorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'av_flow_monitor')]
#[ORM\Index(name: '', columns: ['av_flow_monitor_id'])]
#[ORM\Entity(repositoryClass: AvFlowMonitorRepository::class)]
class AvFlowMonitor implements EntityInterface
{
	public const STATUS_REQUESTED = 1;
	public const STATUS_RUNNING = 2;
	public const STATUS_FINISHED = 3;
	public const STATUS_FAILED = -1;

	#[ORM\Id]
	#[ORM\Column(name: 'av_flow_monitor_id', type: 'string')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'monitors')]
	#[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $requestedBy;

	#[ORM\ManyToOne(targetEntity: AvFlow::class, inversedBy: 'monitors')]
	#[ORM\JoinColumn(name: 'av_flow', referencedColumnName: 'id', nullable: false)]
	private AvFlow $flow;

	#[ORM\Column(name: 'status', type: 'integer', nullable: false)]
	private int $status = self::STATUS_REQUESTED;

	#[ORM\Column(name: 'requested_at', type: 'datetime', nullable: false)]
	private \DateTimeInterface $requestedAt;

	#[ORM\Column(name: 'started_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $startedAt;

	#[ORM\Column(name: 'finished_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $finishedAt;

	#[ORM\Column(name: 'details', type: 'json', nullable: true)]
	private ?array $details = [];

	#[ORM\Column(name: 'result', type: 'json', nullable: true)]
	private ?array $result = [];

	#[ORM\Column(name: 'auxiliary_data', type: 'json', nullable: true)]
	private ?array $auxiliaryData = [];

	public function __construct()
	{
		$this->requestedAt = new \DateTime('now');
		$this->id = Uuid::v7();
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function setId(string $id): void
	{
		$this->id = $id;
	}

	public function getRequestedBy(): ?InternalUser
	{
		return $this->requestedBy;
	}

	public function setRequestedBy(?InternalUser $requestedBy): void
	{
		$this->requestedBy = $requestedBy;
	}

	public function getFlow(): AvFlow
	{
		return $this->flow;
	}

	public function setFlow(AvFlow $flow): void
	{
		$this->flow = $flow;
	}

	public function getStatus(): int
	{
		return $this->status;
	}

	public function setStatus(int $status): void
	{
		$this->status = $status;
	}

	public function getRequestedAt(): \DateTimeInterface
	{
		return $this->requestedAt;
	}

	public function setRequestedAt(\DateTimeInterface $requestedAt): void
	{
		$this->requestedAt = $requestedAt;
	}

	public function getStartedAt(): ?\DateTimeInterface
	{
		return $this->startedAt;
	}

	public function setStartedAt(?\DateTimeInterface $startedAt): void
	{
		$this->startedAt = $startedAt;
	}

	public function getFinishedAt(): ?\DateTimeInterface
	{
		return $this->finishedAt;
	}

	public function setFinishedAt(?\DateTimeInterface $finishedAt): void
	{
		$this->finishedAt = $finishedAt;
	}

	public function getDetails(): ?array
	{
		return $this->details;
	}

	public function setDetails(?array $details): void
	{
		$this->details = $details;
	}

	public function getResult(): ?array
	{
		return $this->result;
	}

	public function setResult(?array $result): void
	{
		$this->result = $result;
	}

	public function getAuxiliaryData(): ?array
	{
		return $this->auxiliaryData;
	}

	public function setAuxiliaryData(?array $auxiliaryData): void
	{
		$this->auxiliaryData = $auxiliaryData;
	}
}
