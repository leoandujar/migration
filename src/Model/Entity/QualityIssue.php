<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'ap_quality_issue')]
#[ORM\Index(name: '', columns: ['id'])]
#[ORM\Entity]
class QualityIssue implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'quality_issue_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: QualityReport::class, inversedBy: 'qualityCategories')]
	#[ORM\JoinColumn(name: 'quality_report_id', referencedColumnName: 'quality_report_id', nullable: false)]
	private QualityReport $qualityReport;

	#[ORM\ManyToOne(targetEntity: QualityCategory::class, inversedBy: 'qualityReports')]
	#[ORM\JoinColumn(name: 'quality_category_id', referencedColumnName: 'quality_category_id', nullable: false)]
	private QualityCategory $qualityCategory;

	#[ORM\Column(name: 'minor', type: 'integer', nullable: false)]
	private int $minor;

	#[ORM\Column(name: 'major', type: 'integer', nullable: false)]
	private int $major;

	#[ORM\Column(name: 'critical', type: 'integer', nullable: false)]
	private int $critical;

	#[ORM\Column(name: 'comment', type: 'text', length: 65535, nullable: true)]
	private ?string $comment;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getMinor(): ?int
	{
		return $this->minor;
	}

	public function setMinor(int $minor): self
	{
		$this->minor = $minor;

		return $this;
	}

	public function getMajor(): ?int
	{
		return $this->major;
	}

	public function setMajor(int $major): self
	{
		$this->major = $major;

		return $this;
	}

	public function getCritical(): ?int
	{
		return $this->critical;
	}

	public function setCritical(int $critical): self
	{
		$this->critical = $critical;

		return $this;
	}

	public function getComment(): ?string
	{
		return $this->comment;
	}

	public function setComment(?string $comment): self
	{
		$this->comment = $comment;

		return $this;
	}

	public function getQualityReport(): ?QualityReport
	{
		return $this->qualityReport;
	}

	public function setQualityReport(?QualityReport $qualityReport): self
	{
		$this->qualityReport = $qualityReport;

		return $this;
	}

	public function getQualityCategory(): ?QualityCategory
	{
		return $this->qualityCategory;
	}

	public function setQualityCategory(?QualityCategory $qualityCategory): self
	{
		$this->qualityCategory = $qualityCategory;

		return $this;
	}
}
