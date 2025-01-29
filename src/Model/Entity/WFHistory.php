<?php

namespace App\Model\Entity;

use App\Model\Repository\WFHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_workflow_history')]
#[ORM\Entity(repositoryClass: WFHistoryRepository::class)]
class WFHistory
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'wf_history_id_seq', initialValue: 1)]
	#[ORM\Column(name: 'wf_history_id', type: 'bigint', options: ['unsigned' => true])]
	private string $id;

	#[ORM\Column(name: 'workflow_id', type: 'bigint', nullable: true)]
	private string|null $workflowId;

	#[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
	private string|null $name;

	#[ORM\Column(name: 'total_files', type: 'string', length: 255, nullable: true)]
	private string|null $totalFiles;

	#[ORM\Column(name: 'processed_files', type: 'text', nullable: true)]
	private string|null $processedFiles;

	#[ORM\Column(name: 'link', type: 'string', length: 255, nullable: true)]
	private string|null $link;

	#[ORM\Column(name: 'info', type: 'text', nullable: true)]
	private string|null $info;

	#[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
	private \DateTime|null $createdAt;

	#[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
	private \DateTime|null $updatedAt;

	#[ORM\Column(name: 'marking', type: 'json', nullable: true)]
	private array|null $marking = [];

	#[ORM\Column(name: 'context', type: 'json', nullable: true)]
	private array|null $context = [];

	#[ORM\Column(name: 'expires_at', type: 'datetime', nullable: true)]
	private \DateTime $expiresAt;

	#[ORM\Column(name: 'cloud_name', type: 'string', length: 1000, nullable: true)]
	private string|null $cloudName;

	#[ORM\Column(name: 'provider', type: 'string', nullable: true)]
	private string|null $provider;

	#[ORM\Column(name: 'removed', type: 'boolean', nullable: true)]
	private bool $removed = false;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getWorkflowId(): ?string
	{
		return $this->workflowId;
	}

	public function setWorkflowId(?int $workflowId): self
	{
		$this->workflowId = $workflowId;

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

	public function getTotalFiles(): ?string
	{
		return $this->totalFiles;
	}

	public function setTotalFiles(?string $totalFiles): self
	{
		$this->totalFiles = $totalFiles;

		return $this;
	}

	public function getProcessedFiles(): ?string
	{
		return $this->processedFiles;
	}

	public function setProcessedFiles(?string $processedFiles): self
	{
		$this->processedFiles = $processedFiles;

		return $this;
	}

	public function getLink(): ?string
	{
		return $this->link;
	}

	public function setLink(?string $link): self
	{
		$this->link = $link;

		return $this;
	}

	public function getInfo(): ?string
	{
		return $this->info;
	}

	public function setInfo(?string $info): self
	{
		$this->info = $info;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(?\DateTimeInterface $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	public function getMarking(): ?array
	{
		return $this->marking;
	}

	public function setMarking(array $marking = null, $context = []): self
	{
		$this->marking = $marking;
		if (!empty($context)) {
			$this->context = $context;
		}

		return $this;
	}

	public function getContext(): ?array
	{
		return $this->context;
	}

	public function setContext(?array $context): self
	{
		$this->context = $context;

		return $this;
	}

	public function getExpiresAt(): ?\DateTime
	{
		return $this->expiresAt;
	}

	public function setExpiresAt(\DateTime $expiresAt): self
	{
		$this->expiresAt = $expiresAt;

		return $this;
	}

	public function getCloudName(): ?string
	{
		return $this->cloudName;
	}

	public function setCloudName(?string $cloudName): self
	{
		$this->cloudName = $cloudName;

		return $this;
	}

	public function getProvider(): ?string
	{
		return $this->provider;
	}

	public function setProvider(?string $provider): self
	{
		$this->provider = $provider;

		return $this;
	}

	public function getRemoved(): ?bool
	{
		return $this->removed;
	}

	public function setRemoved(bool $removed): self
	{
		$this->removed = $removed;

		return $this;
	}

	public static function instance(WFWorkflow $workflow): self
	{
		$instance = new self();
		$now = new \DateTime();
		$instance->setCreatedAt($now);
		$instance->setWorkflowId($workflow->getId());
		$instance->setName($workflow->getName());
		$instance->setInfo(sprintf('Date: %s', $now->format('Y-m-d H:i:s')));
		$instance->setRemoved(false);
		$instance->setContext($workflow->getParameters()->getParams());

		return $instance;
	}
}
