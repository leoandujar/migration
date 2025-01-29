<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'ap_quality_evaluation')]
#[ORM\Index(columns: ['ap_quality_evaluation_id'], name: '')]
#[ORM\Entity]
class APQualityEvaluation implements EntityInterface
{
	public const TYPE_EPC = 'EPC';
	public const TYPE_EPM = 'EPM';
	public const STATUS_DRAFT = 'DRAFT';
	public const STATUS_FINISHED = 'FINISHED';

	#[ORM\Id]
	#[ORM\Column(name: 'ap_quality_evaluation_id', type: 'uuid', unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: UuidGenerator::class)]
	private ?Uuid $id = null;

	#[ORM\ManyToOne(targetEntity: 'InternalUser', cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'evaluatee_id', referencedColumnName: 'internal_user_id', nullable: false)]
	private InternalUser $evaluatee;

	#[ORM\ManyToOne(targetEntity: 'InternalUser', cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'evaluator_id', referencedColumnName: 'internal_user_id', nullable: false)]
	private InternalUser $evaluator;

	#[ORM\Column(name: 'score', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $score;

	#[ORM\Column(name: 'status', type: 'text', nullable: false)]
	private string $status;

	/**
	 * @var \DateTime|null
	 */
	#[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdAt;

	/**
	 * @var \DateTime|null
	 */
	#[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $updatedAt;

	#[ORM\Column(name: 'type', type: 'string', length: 10, nullable: false)]
	private string $type;

	#[ORM\OneToMany(targetEntity: 'APQualityEvaluationRecord', mappedBy: 'evaluation', cascade: ['persist'], orphanRemoval: true, fetch: 'EAGER')]
	protected mixed $records;

	#[ORM\Column(name: 'excellent', type: 'boolean', nullable: false)]
	private bool $excellent = false;

	#[ORM\Column(name: 'comment', type: 'string', nullable: true)]
	private ?string $comment;

	public function __construct()
	{
		$this->records = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id->__toString();
	}

	public function getScore(): ?float
	{
		return $this->score;
	}

	public function setScore(?float $score): self
	{
		$this->score = $score;

		return $this;
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

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

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function isExcellent(): ?bool
	{
		return $this->excellent;
	}

	public function setExcellent(bool $excellent): self
	{
		$this->excellent = $excellent;

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

	public function getEvaluatee(): ?InternalUser
	{
		return $this->evaluatee;
	}

	public function setEvaluatee(?InternalUser $evaluatee): self
	{
		$this->evaluatee = $evaluatee;

		return $this;
	}

	public function getEvaluator(): ?InternalUser
	{
		return $this->evaluator;
	}

	public function setEvaluator(?InternalUser $evaluator): self
	{
		$this->evaluator = $evaluator;

		return $this;
	}

	/**
	 * @return Collection<int, APQualityEvaluationRecord>
	 */
	public function getRecords(): Collection
	{
		return $this->records;
	}

	public function addRecord(APQualityEvaluationRecord $record): self
	{
		if (!$this->records->contains($record)) {
			$this->records->add($record);
			$record->setEvaluation($this);
		}

		return $this;
	}

	public function removeRecord(APQualityEvaluationRecord $record): self
	{
		if ($this->records->removeElement($record)) {
			// set the owning side to null (unless already changed)
			if ($record->getEvaluation() === $this) {
				$record->setEvaluation(null);
			}
		}

		return $this;
	}
}
