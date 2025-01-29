<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_report_type')]
#[ORM\Index(columns: ['name'])]
#[ORM\UniqueConstraint(name: '', columns: ['name'])]
#[ORM\UniqueConstraint(name: '', columns: ['code'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['name'], message: 'Name already used.')]
class AVReportType
{
	public const CHART_TYPE_NONE = 1;
	public const CHART_TYPE_TABLE = 2;
	public const CHART_TYPE_WIDGET = 3;
	public const CHART_TYPE_LINE = 4;
	public const CHART_TYPE_AREA = 5;
	public const CHART_TYPE_COLUMN = 6;
	public const CHART_TYPE_BAR = 7;
	public const CHART_TYPE_PIE = 8;
	public const CHART_TYPE_TREND = 9;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'av_report_type_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'av_report_type_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'description', type: 'string', length: 255, nullable: false)]
	private string $description;

	#[ORM\Column(name: 'function_name', type: 'string', length: 255, nullable: true)]
	private ?string $functionName;

	#[ORM\Column(name: 'code', type: 'string', nullable: true, unique: true)]
	private ?string $code;

	#[ORM\ManyToOne(targetEntity: AVReportType::class, inversedBy: 'children')]
	#[ORM\JoinColumn(name: 'parent', referencedColumnName: 'av_report_type_id', nullable: true)]
	private ?AVReportType $parent;

	#[ORM\OneToMany(targetEntity: AVReportType::class, mappedBy: 'parent', cascade: ['persist'])]
	private mixed $children;

	#[ORM\OneToMany(targetEntity: AVChart::class, mappedBy: 'reportType', cascade: ['persist'])]
	private mixed $charts;

	public function __construct()
	{
		$this->children = new ArrayCollection();
		$this->charts = new ArrayCollection();
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

	/**
	 * @return mixed
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFunctionName(): ?string
	{
		return $this->functionName;
	}

	/**
	 * @return mixed
	 */
	public function setFunctionName(?string $functionName): self
	{
		$this->functionName = $functionName;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getParent(): ?self
	{
		return $this->parent;
	}

	/**
	 * @return mixed
	 */
	public function setParent(?self $parent): self
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * @return Collection|AVReportType[]
	 */
	public function getChildren(): Collection
	{
		return $this->children;
	}

	/**
	 * @return mixed
	 */
	public function addChild(AVReportType $child): self
	{
		if (!$this->children->contains($child)) {
			$this->children[] = $child;
			$child->setParent($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeChild(AVReportType $child): self
	{
		if ($this->children->removeElement($child)) {
			// set the owning side to null (unless already changed)
			if ($child->getParent() === $this) {
				$child->setParent(null);
			}
		}

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
			$chart->setReportType($this);
		}

		return $this;
	}

	public function removeChart(AVChart $chart): self
	{
		if ($this->charts->removeElement($chart)) {
			// set the owning side to null (unless already changed)
			if ($chart->getReportType() === $this) {
				$chart->setReportType(null);
			}
		}

		return $this;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(?string $code): self
	{
		$this->code = $code;

		return $this;
	}
}
