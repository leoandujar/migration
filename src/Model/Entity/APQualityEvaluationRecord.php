<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'ap_quality_evaluation_record')]
#[ORM\Index(columns: ['ap_quality_evaluation_record_id'], name: '')]
#[ORM\Entity]
class APQualityEvaluationRecord implements EntityInterface
{
	#[ORM\Id]
	#[ORM\Column(name: 'ap_quality_evaluation_record_id', type: 'uuid', unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: UuidGenerator::class)]
	private ?Uuid $id;

	#[ORM\ManyToOne(targetEntity: 'APQualityEvaluation', inversedBy: 'records')]
	#[ORM\JoinColumn(name: 'ap_quality_evaluation_id', referencedColumnName: 'ap_quality_evaluation_id', nullable: false)]
	private APQualityEvaluation $evaluation;

	#[ORM\ManyToOne(targetEntity: 'QualityCategory')]
	#[ORM\JoinColumn(name: 'quality_category_id', referencedColumnName: 'quality_category_id', nullable: false)]
	private QualityCategory $category;

	#[ORM\Column(name: 'value', type: 'integer', nullable: false)]
	private int $value = 0;

	#[ORM\Column(name: 'comment', type: 'text', length: 65535, nullable: true)]
	private ?string $comment;

	public function getId(): ?string
	{
		return $this->id->__toString();
	}

	public function getValue(): ?int
	{
		return $this->value;
	}

	public function setValue(int $value): self
	{
		$this->value = $value;

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

	public function getEvaluation(): ?APQualityEvaluation
	{
		return $this->evaluation;
	}

	public function setEvaluation(?APQualityEvaluation $evaluation): self
	{
		$this->evaluation = $evaluation;

		return $this;
	}

	public function getCategory(): ?QualityCategory
	{
		return $this->category;
	}

	public function setCategory(?QualityCategory $category): self
	{
		$this->category = $category;

		return $this;
	}
}
