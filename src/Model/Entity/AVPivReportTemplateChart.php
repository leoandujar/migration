<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_reports_templates_charts')]
#[ORM\UniqueConstraint(name: '', columns: ['template_id', 'chart_id'])]
#[ORM\Entity]
class AVPivReportTemplateChart
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'av_piv_report_template_type_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'av_piv_report_template_type_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: AVReportTemplate::class, inversedBy: 'reportTypeList')]
	#[ORM\JoinColumn(name: 'template_id', referencedColumnName: 'av_report_template_id', nullable: false, onDelete: 'CASCADE')]
	private AVReportTemplate $template;

	#[ORM\ManyToOne(targetEntity: AVChart::class, inversedBy: 'templateList')]
	#[ORM\JoinColumn(name: 'chart_id', referencedColumnName: 'av_report_chart_id', nullable: false, onDelete: 'CASCADE')]
	private AVChart $chart;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getTemplate(): ?AVReportTemplate
	{
		return $this->template;
	}

	public function setTemplate(?AVReportTemplate $template): self
	{
		$this->template = $template;

		return $this;
	}

	public function getChart(): ?AVChart
	{
		return $this->chart;
	}

	public function setChart(?AVChart $chart): self
	{
		$this->chart = $chart;

		return $this;
	}
}
