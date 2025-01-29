<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'ap_quality_category')]
#[ORM\Index(name: '', columns: ['quality_category_id'])]
#[ORM\Entity]
class QualityCategory implements EntityInterface
{
	public const CATEGORY_DQA = 'DQA';
	public const CATEGORY_PQA = 'PQA';
	public const CATEGORY_PME = 'PME';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'quality_category_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'quality_category_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: QualityCategory::class)]
	#[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'quality_category_id', nullable: true)]
	private ?QualityCategory $parentCategory;

	#[ORM\Column(name: 'name', type: 'text', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'weight', type: 'integer', nullable: false)]
	private int $weight;

	#[ORM\Column(name: 'is_leaf', type: 'boolean', nullable: false)]
	private bool $isLeaf;

	#[ORM\Column(name: 'is_other', type: 'boolean', nullable: false)]
	private bool $isOther;

	#[ORM\Column(name: 'path', type: 'text', nullable: false)]
	private string $path;

	#[ORM\Column(name: 'path_depth', type: 'text', nullable: false)]
	private string $pathDepth;

	#[ORM\Column(name: 'parent_name', type: 'text', nullable: false)]
	private string $parentName;

	#[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdDate;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'label', type: 'string', length: 255, nullable: true)]
	private ?string $label;

	#[ORM\Column(name: 'type', type: 'string', length: 10, nullable: false)]
	private string $type;

	#[ORM\Column(name: 'required', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $required;

	#[ORM\OneToMany(targetEntity: QualityAnswer::class, mappedBy: 'qualityCategory', cascade: ['persist'], orphanRemoval: true, fetch: 'EAGER')]
	protected mixed $qualityAnswers;

	#[ORM\OneToMany(targetEntity: QualityIssue::class, mappedBy: 'qualityCategory', orphanRemoval: true)]
	protected mixed $qualityIssues;

	public function __construct()
	{
		$this->qualityIssues = new ArrayCollection();
		$this->qualityAnswers = new ArrayCollection();
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

	public function getWeight(): ?int
	{
		return $this->weight;
	}

	public function setWeight(int $weight): self
	{
		$this->weight = $weight;

		return $this;
	}

	public function getIsLeaf(): ?bool
	{
		return $this->isLeaf;
	}

	public function setIsLeaf(bool $isLeaf): self
	{
		$this->isLeaf = $isLeaf;

		return $this;
	}

	public function getIsOther(): ?bool
	{
		return $this->isOther;
	}

	public function setIsOther(bool $isOther): self
	{
		$this->isOther = $isOther;

		return $this;
	}

	public function getPath(): ?string
	{
		return $this->path;
	}

	public function setPath(string $path): self
	{
		$this->path = $path;

		return $this;
	}

	public function getPathDepth(): ?string
	{
		return $this->pathDepth;
	}

	public function setPathDepth(string $pathDepth): self
	{
		$this->pathDepth = $pathDepth;

		return $this;
	}

	public function getParentName(): ?string
	{
		return $this->parentName;
	}

	public function setParentName(string $parentName): self
	{
		$this->parentName = $parentName;

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

	public function getParentCategory(): ?self
	{
		return $this->parentCategory;
	}

	public function setParentCategory(?self $parentCategory): self
	{
		$this->parentCategory = $parentCategory;

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
			$qualityIssue->setQualityCategory($this);
		}

		return $this;
	}

	public function removeQualityIssue(QualityIssue $qualityIssue): self
	{
		if ($this->qualityIssues->removeElement($qualityIssue)) {
			// set the owning side to null (unless already changed)
			if ($qualityIssue->getQualityCategory() === $this) {
				$qualityIssue->setQualityCategory(null);
			}
		}

		return $this;
	}

	public function getLabel(): ?string
	{
		return $this->label;
	}

	public function setLabel(?string $label): self
	{
		$this->label = $label;

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

	public function getRequired(): ?bool
	{
		return $this->required;
	}

	public function setRequired(bool $required): self
	{
		$this->required = $required;

		return $this;
	}

	public function getQualityAnswers(): Collection
	{
		return $this->qualityAnswers;
	}

	public function addQualityAnswer(QualityAnswer $qualityAnswer): self
	{
		if (!$this->qualityAnswers->contains($qualityAnswer)) {
			$this->qualityAnswers[] = $qualityAnswer;
			$qualityAnswer->setQualityCategory($this);
		}

		return $this;
	}

	public function removeQualityAnswer(QualityAnswer $qualityAnswer): self
	{
		if ($this->qualityAnswers->removeElement($qualityAnswer)) {
			// set the owning side to null (unless already changed)
			if ($qualityAnswer->getQualityCategory() === $this) {
				$qualityAnswer->setQualityCategory(null);
			}
		}

		return $this;
	}
}
