<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'av_report_chart')]
#[ORM\Entity]
class AVChart
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
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'av_report_chart_id', type: 'guid')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', unique: true, nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'slug', type: 'string', unique: true, nullable: false)]
	private string $slug;

	#[ORM\Column(name: 'category', type: 'string', length: 255, nullable: true)]
	private ?string $category;

	#[ORM\Column(name: 'type', type: 'integer', nullable: false, options: ['default' => 1])]
	private int $type = self::CHART_TYPE_PIE;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $active;

	#[ORM\Column(name: 'size', type: 'integer', nullable: true, options: ['default' => 12])]
	private ?int $size;

	#[ORM\ManyToOne(targetEntity: AVReportType::class, inversedBy: 'charts')]
	#[ORM\JoinColumn(name: 'report_type', referencedColumnName: 'av_report_type_id', nullable: true)]
	private ?AVReportType $reportType;

	#[ORM\Column(name: 'return_y', type: 'string', length: 50, nullable: true)]
	private ?string $returnY;

	#[ORM\JoinTable(name: 'av_report_chart_group')]
	#[ORM\JoinColumn(name: 'chart', referencedColumnName: 'av_report_chart_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'chart_group', referencedColumnName: 'av_category_group_id')]
	#[ORM\ManyToMany(targetEntity: CategoryGroup::class, inversedBy: 'charts', cascade: ['persist'])]
	protected mixed $groups;

	#[ORM\Column(name: 'options', type: 'json', nullable: true)]
	private ?array $options;

	#[ORM\OneToMany(targetEntity: AVPivReportTemplateChart::class, mappedBy: 'chart', cascade: ['persist'])]
	private mixed $templateList;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->groups = new ArrayCollection();
		$this->templateList = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getSlug(): ?string
	{
		return $this->slug;
	}

	/**
	 * @return mixed
	 */
	public function setSlug(string $slug): self
	{
		$this->slug = $slug;

		return $this;
	}

	public function getCategory(): ?string
	{
		return $this->category;
	}

	/**
	 * @return mixed
	 */
	public function setCategory(string $category): self
	{
		$this->category = $category;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getGroups(): Collection
	{
		return $this->groups;
	}

	/**
	 * @return mixed
	 */
	public function addGroup(CategoryGroup $group): self
	{
		if (!$this->groups->contains($group)) {
			$this->groups[] = $group;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeGroup(CategoryGroup $group): self
	{
		if ($this->groups->contains($group)) {
			$this->groups->removeElement($group);
		}

		return $this;
	}

	public function getSize(): ?int
	{
		return $this->size;
	}

	/**
	 * @return mixed
	 */
	public function setSize(?int $size): self
	{
		$this->size = $size;

		return $this;
	}

	public function getReportType(): ?AVReportType
	{
		return $this->reportType;
	}

	public function setReportType(?AVReportType $reportType): self
	{
		$this->reportType = $reportType;

		return $this;
	}

	public function getOptions(): ?array
	{
		return $this->options;
	}

	public function setOptions(?array $options): self
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * @return Collection|AVPivReportTemplateChart[]
	 */
	public function getTemplateList(): Collection
	{
		return $this->templateList;
	}

	public function addTemplateList(AVPivReportTemplateChart $templateList): self
	{
		if (!$this->templateList->contains($templateList)) {
			$this->templateList[] = $templateList;
			$templateList->setChart($this);
		}

		return $this;
	}

	public function removeTemplateList(AVPivReportTemplateChart $templateList): self
	{
		if ($this->templateList->removeElement($templateList)) {
			// set the owning side to null (unless already changed)
			if ($templateList->getChart() === $this) {
				$templateList->setChart(null);
			}
		}

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

	public function isActive(): ?bool
	{
		return $this->active;
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

	public function getReturnY(): ?string
	{
		return $this->returnY;
	}

	public function setReturnY(?string $returnY): self
	{
		$this->returnY = $returnY;

		return $this;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	public function setType(int $type): self
	{
		$this->type = $type;

		return $this;
	}
}
