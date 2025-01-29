<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_workflow')]
#[ORM\Entity]
class WFWorkflow
{
	public const TYPE_XTRF_PROJECT = 1;
	public const TYPE_CREATE_ZIP = 2;
	public const TYPE_XTM_PROJECT = 3;
	public const TYPE_XTM_GITHUB = 4;
	public const TYPE_EMAIL_PARSING = 5;
	public const TYPE_XTM_TM = 6;
	public const TYPE_ATTESTATION = 8;
	public const TYPE_XTRF_QBO = 9;
	public const TYPE_BL_XTRF = 10;
	public const TYPE_XTRF_PROJECT_V2 = 11;
	public const TYPE_FTP_XTRF = 12;
	public const TYPE_XTRF_FTP = 13;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'wf_workflow_id_seq', initialValue: 1)]
	#[ORM\Column(name: 'wf_workflow_id', type: 'bigint', options: ['unsigned' => true])]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
	private ?\DateTime $createdAt;

	#[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
	private ?\DateTime $updatedAt;

	#[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
	private ?\DateTime $deletedAt;

	#[ORM\Column(name: 'workflow_type', length: 15, nullable: true)]
	private ?string $type;

	#[ORM\Column(name: 'run_automatically', type: 'boolean', nullable: true)]
	private ?bool $runAutomatically = false;

	#[ORM\Column(name: 'last_run_at', type: 'datetime', nullable: true)]
	private ?\DateTime $lastRunAt;

	#[ORM\Column(name: 'run_pattern', type: 'string', length: 50, nullable: true)]
	private ?string $runPattern;

	#[ORM\JoinTable(name: 'av_workflow_category_group')]
	#[ORM\JoinColumn(name: 'workflow', referencedColumnName: 'wf_workflow_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'category_group', referencedColumnName: 'av_category_group_id')]
	#[ORM\ManyToMany(targetEntity: CategoryGroup::class, inversedBy: 'workflows', cascade: ['persist'])]
	protected mixed $categoryGroups;

	#[ORM\OneToOne(mappedBy: 'workflow', targetEntity: WFParams::class, cascade: ['persist', 'remove'])]
	private WFParams $parameters;

	#[ORM\OneToMany(mappedBy: 'workflow', targetEntity: AVWorkflowMonitor::class)]
	private mixed $workflowMonitors;

	public function __construct()
	{
		$this->workflowMonitors = new ArrayCollection();
		$this->categoryGroups = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getCategoryGroupsCodes(): array
	{
		$list = [];
		/** @var CategoryGroup $categoryGroup */
		foreach ($this->categoryGroups as $categoryGroup) {
			$list[] = $categoryGroup->getCode();
		}

		return $list;
	}

	public function setId(int $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

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

	public function getDeletedAt(): ?\DateTimeInterface
	{
		return $this->deletedAt;
	}

	public function setDeletedAt(?\DateTimeInterface $deletedAt): self
	{
		$this->deletedAt = $deletedAt;

		return $this;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getParameters(): ?WFParams
	{
		return $this->parameters;
	}

	public function setParameters(?WFParams $parameters): self
	{
		$this->parameters = $parameters;

		// set (or unset) the owning side of the relation if necessary
		$newWorkflow = null === $parameters ? null : $this;
		if ($parameters->getWorkflow() !== $newWorkflow) {
			$parameters->setWorkflow($newWorkflow);
		}

		return $this;
	}

	public function getWorkflowMonitors(): Collection
	{
		return $this->workflowMonitors;
	}

	public function addWorkflowMonitor(AVWorkflowMonitor $workflowMonitor): self
	{
		if (!$this->workflowMonitors->contains($workflowMonitor)) {
			$this->workflowMonitors[] = $workflowMonitor;
			$workflowMonitor->setWorkflow($this);
		}

		return $this;
	}

	public function removeWorkflowMonitor(AVWorkflowMonitor $workflowMonitor): self
	{
		if ($this->workflowMonitors->removeElement($workflowMonitor)) {
			// set the owning side to null (unless already changed)
			if ($workflowMonitor->getWorkflow() === $this) {
				$workflowMonitor->setWorkflow(null);
			}
		}

		return $this;
	}

	public function isRunAutomatically(): ?bool
	{
		return $this->runAutomatically;
	}

	public function setRunAutomatically(?bool $runAutomatically): self
	{
		$this->runAutomatically = $runAutomatically;

		return $this;
	}

	public function getLastRunAt(): ?\DateTimeInterface
	{
		return $this->lastRunAt;
	}

	public function setLastRunAt(?\DateTimeInterface $lastRunAt): self
	{
		$this->lastRunAt = $lastRunAt;

		return $this;
	}

	public function getRunPattern(): ?string
	{
		return $this->runPattern;
	}

	public function setRunPattern(?string $runPattern): self
	{
		$this->runPattern = $runPattern;

		return $this;
	}

	public function getCategoryGroups(): Collection
	{
		return $this->categoryGroups;
	}

	public function addCategoryGroup(CategoryGroup $categoryGroup): self
	{
		if (!$this->categoryGroups->contains($categoryGroup)) {
			$this->categoryGroups[] = $categoryGroup;
		}

		return $this;
	}

	public function removeCategoryGroup(CategoryGroup $categoryGroup): self
	{
		$this->categoryGroups->removeElement($categoryGroup);

		return $this;
	}
}
