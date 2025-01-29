<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'ap_quality_answer')]
#[ORM\Index(name: '', columns: ['quality_answer_id'])]
#[ORM\Entity]
class QualityAnswer implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'quality_answer_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'quality_answer_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: QualityCategory::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'quality_category_id', referencedColumnName: 'quality_category_id', nullable: false)]
	private QualityCategory $qualityCategory;

	#[ORM\Column(name: 'label', type: 'string', nullable: false)]
	private string $label;

	#[ORM\Column(name: 'score', type: 'integer', nullable: false)]
	private int $score;

	public function getId(): ?string
	{
		return $this->id;
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

	public function getScore(): ?int
	{
		return $this->score;
	}

	public function setScore(int $score): self
	{
		$this->score = $score;

		return $this;
	}

	public function getLabel(): ?string
	{
		return $this->label;
	}

	public function setLabel(string $label): self
	{
		$this->label = $label;

		return $this;
	}
}
