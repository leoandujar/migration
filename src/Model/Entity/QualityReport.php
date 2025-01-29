<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'ap_quality_report')]
#[ORM\Index(name: '', columns: ['quality_report_id'])]
#[ORM\Entity]
class QualityReport implements EntityInterface
{
	public const CATEGORY_DQA = 'DQA';
	public const CATEGORY_PQA = 'PQA';
	public const CATEGORY_PME = 'PME';

	public const STATUS_DRAFT = 'DRAFT';
	public const STATUS_FINISHED = 'FINISHED';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'quality_report_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'quality_report_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: Activity::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'activity_id', referencedColumnName: 'activity_id', nullable: false)]
	private Activity $activity;

	#[ORM\Column(name: 'proofer_name', type: 'text', nullable: false)]
	private string $prooferName;

	#[ORM\Column(name: 'page_count', type: 'integer', nullable: false)]
	private int $pageCount;

	#[ORM\Column(name: 'format', type: 'text', nullable: false)]
	private string $format;

	#[ORM\Column(name: 'score', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $score = null;

	#[ORM\Column(name: 'minor_multiplier', type: 'integer', nullable: false, options: ['default' => '1'])]
	private int $minorMultiplier = 1;

	#[ORM\Column(name: 'major_multiplier', type: 'integer', nullable: false, options: ['default' => '5'])]
	private int $majorMultiplier = 5;

	#[ORM\Column(name: 'critical_multiplier', type: 'integer', nullable: false, options: ['default' => '9'])]
	private int $criticalMultiplier = 9;

	#[ORM\Column(name: 'status', type: 'text', nullable: false)]
	private string $status;

	#[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdDate;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'type', type: 'string', length: 10, nullable: false)]
	private string $type;

	#[ORM\OneToMany(targetEntity: QualityIssue::class, mappedBy: 'qualityReport', cascade: ['persist'], fetch: 'EAGER', orphanRemoval: true)]
	protected mixed $qualityIssues;

	#[ORM\Column(name: 'excellent', type: 'boolean', nullable: false)]
	private bool $excellent;

	#[ORM\Column(name: 'comment', type: 'string', nullable: true, options: ['default' => 'false'])]
	private ?string $comment;

	public function __construct()
	{
		$this->qualityIssues = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getProoferName(): ?string
	{
		return $this->prooferName;
	}

	public function setProoferName(string $prooferName): self
	{
		$this->prooferName = $prooferName;

		return $this;
	}

	public function getPageCount(): ?int
	{
		return $this->pageCount;
	}

	public function setPageCount(int $pageCount): self
	{
		$this->pageCount = $pageCount;

		return $this;
	}

	public function getFormat(): ?string
	{
		return $this->format;
	}

	public function setFormat(string $format): self
	{
		$this->format = $format;

		return $this;
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

	public function getCreatedDate(): ?\DateTimeInterface
	{
		return $this->createdDate;
	}

	public function setCreatedDate(?\DateTimeInterface $createdDate): self
	{
		$this->createdDate = $createdDate;

		return $this;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getQualityIssues(): Collection
	{
		return $this->qualityIssues;
	}

	public function addQualityIssue(QualityIssue $qualityIssue): self
	{
		if (!$this->qualityIssues->contains($qualityIssue)) {
			$this->qualityIssues[] = $qualityIssue;
			$qualityIssue->setQualityReport($this);
		}

		return $this;
	}

	public function removeQualityIssue(QualityIssue $qualityIssue): self
	{
		if ($this->qualityIssues->removeElement($qualityIssue)) {
			// set the owning side to null (unless already changed)
			if ($qualityIssue->getQualityReport() === $this) {
				$qualityIssue->setQualityReport(null);
			}
		}

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

	public function getActivity(): ?Activity
	{
		return $this->activity;
	}

	public function setActivity(?Activity $activity): self
	{
		$this->activity = $activity;

		return $this;
	}

	public function getMinorMultiplier(): ?int
	{
		return $this->minorMultiplier;
	}

	public function setMinorMultiplier(int $minorMultiplier): self
	{
		$this->minorMultiplier = $minorMultiplier;

		return $this;
	}

	public function getMajorMultiplier(): ?int
	{
		return $this->majorMultiplier;
	}

	public function setMajorMultiplier(int $majorMultiplier): self
	{
		$this->majorMultiplier = $majorMultiplier;

		return $this;
	}

	public function getCriticalMultiplier(): ?int
	{
		return $this->criticalMultiplier;
	}

	public function setCriticalMultiplier(int $criticalMultiplier): self
	{
		$this->criticalMultiplier = $criticalMultiplier;

		return $this;
	}

	public function getExcellent(): ?bool
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

	public function setComment(string $comment): self
	{
		$this->comment = $comment;

		return $this;
	}
}
