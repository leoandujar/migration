<?php

namespace App\Model\Entity;

use App\Model\Repository\AvFlowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'av_flow')]
#[ORM\Entity(repositoryClass: AvFlowRepository::class)]
class AvFlow
{
	#[ORM\Id]
	#[ORM\Column(type: 'string', length: 36, unique: true)]
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
	private ?\DateTime $deletedAt = null;

	#[ORM\Column(name: 'run_automatically', type: 'boolean', nullable: true)]
	private ?bool $runAutomatically = false;

	#[ORM\Column(name: 'last_run_at', type: 'datetime', nullable: true)]
	private ?\DateTime $lastRunAt = null;

	#[ORM\Column(name: 'run_pattern', type: 'string', length: 50, nullable: true)]
	private ?string $runPattern;

	#[ORM\Column(name: 'parameters', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $parameters;

	#[ORM\OneToMany(targetEntity: AvFlowAction::class, mappedBy: 'flow', cascade: ['persist'], orphanRemoval: true)]
	private mixed $actions;

	#[ORM\OneToOne(targetEntity: AvFlowAction::class, cascade: ['remove'], orphanRemoval: true)]
	#[ORM\JoinColumn(name: 'start_action_id', referencedColumnName: 'id', nullable: false)]
	private ?AvFlowAction $startAction;

	#[ORM\ManyToMany(targetEntity: CategoryGroup::class, inversedBy: 'flows', cascade: ['persist'])]
	#[ORM\JoinTable(name: 'av_flow_category_group')]
	#[ORM\JoinColumn(name: 'flow_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'category_group_id', referencedColumnName: 'av_category_group_id')]
	protected ?Collection $categoryGroups;

	#[ORM\OneToMany(mappedBy: 'flow', targetEntity: AvFlowMonitor::class)]
	private Collection $monitors;

	public function __construct()
	{
		$this->id = Uuid::v7()->__toString();
		$this->actions = new ArrayCollection();
		$this->categoryGroups = new ArrayCollection();
		$this->monitors = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(?\DateTimeInterface $createdAt): static
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	public function getDeletedAt(): ?\DateTimeInterface
	{
		return $this->deletedAt;
	}

	public function setDeletedAt(?\DateTimeInterface $deletedAt): static
	{
		$this->deletedAt = $deletedAt;

		return $this;
	}

	public function isRunAutomatically(): ?bool
	{
		return $this->runAutomatically;
	}

	public function setRunAutomatically(?bool $runAutomatically): static
	{
		$this->runAutomatically = $runAutomatically;

		return $this;
	}

	public function getRunAutomatically(): ?bool
	{
		return $this->runAutomatically;
	}

	public function getLastRunAt(): ?\DateTimeInterface
	{
		return $this->lastRunAt;
	}

	public function setLastRunAt(?\DateTimeInterface $lastRunAt): static
	{
		$this->lastRunAt = $lastRunAt;

		return $this;
	}

	public function getRunPattern(): ?string
	{
		return $this->runPattern;
	}

	public function setRunPattern(?string $runPattern): static
	{
		$this->runPattern = $runPattern;

		return $this;
	}

	public function getParameters(): ?array
	{
		return $this->parameters;
	}

	public function setParameters(?array $parameters): static
	{
		$this->parameters = $parameters;

		return $this;
	}

	public function getCategoryGroups(): ?Collection
	{
		return $this->categoryGroups;
	}

	public function addCategoryGroup(CategoryGroup $categoryGroup): static
	{
		if (!$this->categoryGroups->contains($categoryGroup)) {
			$this->categoryGroups->add($categoryGroup);
		}

		return $this;
	}

	public function removeCategoryGroup(CategoryGroup $categoryGroup): static
	{
		$this->categoryGroups->removeElement($categoryGroup);

		return $this;
	}

	public function getMonitors(): Collection
	{
		return $this->monitors;
	}

	public function setMonitors(Collection $monitors): void
	{
		$this->monitors = $monitors;
	}

	/**
	 * @return Collection<int, AvFlowAction>
	 */
	public function getActions(): Collection
	{
		return $this->actions;
	}

	public function addAction(AvFlowAction $action): static
	{
		if (!$this->actions->contains($action)) {
			$this->actions->add($action);
			$action->setFlow($this);
		}

		return $this;
	}

	public function removeAction(AvFlowAction $action): static
	{
		if ($this->actions->removeElement($action)) {
			// set the owning side to null (unless already changed)
			if ($action->getFlow() === $this) {
				$action->setFlow(null);
			}
		}

		return $this;
	}

    public function clearActions(): static
    {
        $this->actions->clear();

        return $this;
    }

    public function clearMonitors(): static
    {
        $this->monitors->clear();

        return $this;
    }


	public function getStartAction(): ?AvFlowAction
	{
		return $this->startAction;
	}

	public function setStartAction(?AvFlowAction $startAction): static
	{
		$this->startAction = $startAction;

		return $this;
	}

	public function addMonitor(AvFlowMonitor $monitor): static
	{
		if (!$this->monitors->contains($monitor)) {
			$this->monitors->add($monitor);
			$monitor->setFlow($this);
		}

		return $this;
	}

	public function removeMonitor(AvFlowMonitor $monitor): static
	{
		if ($this->monitors->removeElement($monitor)) {
			// set the owning side to null (unless already changed)
			if ($monitor->getFlow() === $this) {
				$monitor->setFlow(null);
			}
		}

		return $this;
	}
}
