<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_report_template')]
#[ORM\Index(columns: ['name'])]
#[ORM\UniqueConstraint(name: '', columns: ['name'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['name'], message: 'Name already used.')]
class AVReportTemplate
{
	public const EXPORT_FORMAT_PDF = 1;
	public const EXPORT_FORMAT_EXCEL = 2;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'av_report_template_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'av_report_template_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'filters', type: 'json', nullable: true)]
	private ?array $filters;

	#[ORM\Column(name: 'predefined_data', type: 'json', nullable: true)]
	private ?array $predefinedData;

	#[ORM\Column(name: 'format', type: 'integer', nullable: false)]
	private int $format = self::EXPORT_FORMAT_PDF;

	#[ORM\Column(name: 'template', type: 'string', length: 50, nullable: true)]
	private ?string $template;

	#[ORM\OneToMany(targetEntity: AVPivReportTemplateChart::class, mappedBy: 'template', cascade: ['persist', 'remove'])]
	private mixed $chartList;

	#[ORM\Column(name: 'category_groups', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $categoryGroups;

	public function __construct()
	{
		$this->chartList = new ArrayCollection();
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

	public function getFilters(): ?array
	{
		return $this->filters;
	}

	public function setFilters(?array $filters): self
	{
		$this->filters = $filters;

		return $this;
	}

	public function getFormat(): ?int
	{
		return $this->format;
	}

	public function setFormat(int $format): self
	{
		$this->format = $format;

		return $this;
	}

	public function getTemplate(): ?string
	{
		return $this->template;
	}

	public function setTemplate(?string $template): self
	{
		$this->template = $template;

		return $this;
	}

	/**
	 * @return Collection|AVPivReportTemplateChart[]
	 */
	public function getChartList(): Collection
	{
		return $this->chartList;
	}

	public function addReportTypeList(AVPivReportTemplateChart $reportTypeList): self
	{
		if (!$this->chartList->contains($reportTypeList)) {
			$this->chartList[] = $reportTypeList;
			$reportTypeList->setTemplate($this);
		}

		return $this;
	}

	public function removeReportTypeList(AVPivReportTemplateChart $reportTypeList): self
	{
		if ($this->chartList->removeElement($reportTypeList)) {
			// set the owning side to null (unless already changed)
			if ($reportTypeList->getTemplate() === $this) {
				$reportTypeList->setTemplate(null);
			}
		}

		return $this;
	}

	public function getCategoryGroups(): ?array
	{
		return $this->categoryGroups;
	}

	public function setCategoryGroups(?array $categoryGroups): self
	{
		$this->categoryGroups = $categoryGroups;

		return $this;
	}

	public function getPredefinedData(): ?array
	{
		return $this->predefinedData;
	}

	public function setPredefinedData(?array $predefinedData): self
	{
		$this->predefinedData = $predefinedData;

		return $this;
	}
}
