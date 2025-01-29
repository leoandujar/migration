<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_category_group')]
#[ORM\Index(columns: ['code'])]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['target'])]
#[ORM\UniqueConstraint(name: '', columns: ['code', 'target'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['code', 'target'], message: 'Code already used for that target.')]
class CategoryGroup
{
	public const TARGET_CHART = 1;
	public const TARGET_REPORT_TEMPLATE = 2;
	public const TARGET_AP_WORKFLOW = 3;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'av_category_group_id', type: 'guid')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 50)]
	private string $name;

	#[ORM\Column(name: 'code', type: 'string', length: 150)]
	private string $code;

	#[ORM\Column(name: 'target', type: 'integer', nullable: false, options: ['default' => 1])]
	private int $target = self::TARGET_CHART;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $active;

	#[ORM\ManyToMany(targetEntity: AVChart::class, mappedBy: 'groups', cascade: ['persist'])]
	private mixed $charts;

	#[ORM\ManyToMany(targetEntity: WFWorkflow::class, mappedBy: 'categoryGroups', cascade: ['persist'])]
	private mixed $workflows;

	#[ORM\ManyToMany(targetEntity: AvFlow::class, mappedBy: 'categoryGroups', cascade: ['persist'])]
	private Collection $flows;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->charts = new ArrayCollection();
		$this->workflows = new ArrayCollection();
		$this->flows = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
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

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}

	public function getTarget(): ?int
	{
		return $this->target;
	}

	public function setTarget(int $target): self
	{
		$this->target = $target;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * @return Collection|AVChart[]
	 */
	public function getCharts(): Collection
	{
		return $this->charts;
	}

	public function addChart(AVChart $chart): self
	{
		if (!$this->charts->contains($chart)) {
			$this->charts[] = $chart;
			$chart->addGroup($this);
		}

		return $this;
	}

	public function removeChart(AVChart $chart): self
	{
		if ($this->charts->removeElement($chart)) {
			$chart->removeGroup($this);
		}

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return Collection<int, WFWorkflow>
	 */
	public function getWorkflows(): Collection
	{
		return $this->workflows;
	}

	public function addWorkflow(WFWorkflow $workflow): self
	{
		if (!$this->workflows->contains($workflow)) {
			$this->workflows[] = $workflow;
			$workflow->addCategoryGroup($this);
		}

		return $this;
	}

	public function removeWorkflow(WFWorkflow $workflow): self
	{
		if ($this->workflows->removeElement($workflow)) {
			$workflow->removeCategoryGroup($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, AvFlow>
	 */
	public function getFlows(): Collection
	{
		return $this->flows;
	}

	public function addFlow(AvFlow $flow): self
	{
		if (!$this->flows->contains($flow)) {
			$this->flows[] = $flow;
			$flow->addCategoryGroup($this);
		}

		return $this;
	}

	public function removeFlow(AvFlow $flow): self
	{
		if ($this->flows->removeElement($flow)) {
			$flow->removeCategoryGroup($this);
		}

		return $this;
	}
}
