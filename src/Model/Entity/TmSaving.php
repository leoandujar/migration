<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'tm_savings')]
#[ORM\Entity]
class TmSaving implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'tm_savings_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'tm_savings_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'base_rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $baseRate;

	#[ORM\Column(name: 'cat_tool', type: 'string', nullable: false)]
	private string $catTool;

	#[ORM\Column(name: 'rates_type', type: 'string', nullable: false)]
	private string $ratesType;

	#[ORM\Column(name: 'cat_analysis', type: 'text', nullable: true)]
	private ?string $catAnalysis;

	#[ORM\Column(name: 'rounding_policy', type: 'text', nullable: false, options: ['default' => 'ROUND_LAST::text'])]
	private string $roundingPolicy;

	#[ORM\Column(name: 'original_cat_tool', type: 'string', nullable: true)]
	private ?string $originalCatTool;

	#[ORM\OneToMany(targetEntity: ActivityCatCharge::class, mappedBy: 'tmSavings', orphanRemoval: true)]
	private mixed $activityCatCharge;

	#[ORM\OneToMany(targetEntity: TaskCatCharge::class, mappedBy: 'tmSavings', orphanRemoval: true)]
	private mixed $tasksCatCharge;

	#[ORM\Column(name: 'old_base_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $oldBaseRate;

	public function __construct()
	{
		$this->activityCatCharge = new ArrayCollection();
		$this->tasksCatCharge = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
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

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getBaseRate(): ?string
	{
		return $this->baseRate;
	}

	public function setBaseRate(string $baseRate): self
	{
		$this->baseRate = $baseRate;

		return $this;
	}

	public function getCatTool(): ?string
	{
		return $this->catTool;
	}

	public function setCatTool(string $catTool): self
	{
		$this->catTool = $catTool;

		return $this;
	}

	public function getRatesType(): ?string
	{
		return $this->ratesType;
	}

	public function setRatesType(string $ratesType): self
	{
		$this->ratesType = $ratesType;

		return $this;
	}

	public function getCatAnalysis(): ?string
	{
		return $this->catAnalysis;
	}

	public function setCatAnalysis(?string $catAnalysis): self
	{
		$this->catAnalysis = $catAnalysis;

		return $this;
	}

	public function getRoundingPolicy(): ?string
	{
		return $this->roundingPolicy;
	}

	public function setRoundingPolicy(string $roundingPolicy): self
	{
		$this->roundingPolicy = $roundingPolicy;

		return $this;
	}

	public function getOriginalCatTool(): ?string
	{
		return $this->originalCatTool;
	}

	public function setOriginalCatTool(?string $originalCatTool): self
	{
		$this->originalCatTool = $originalCatTool;

		return $this;
	}

	public function getOldBaseRate(): ?string
	{
		return $this->oldBaseRate;
	}

	public function setOldBaseRate(string $oldBaseRate): self
	{
		$this->oldBaseRate = $oldBaseRate;

		return $this;
	}

	/**
	 * @return Collection<int, ActivityCatCharge>
	 */
	public function getActivityCatCharge(): Collection
	{
		return $this->activityCatCharge;
	}

	public function addActivityCatCharge(ActivityCatCharge $activityCatCharge): self
	{
		if (!$this->activityCatCharge->contains($activityCatCharge)) {
			$this->activityCatCharge[] = $activityCatCharge;
			$activityCatCharge->setTmSavings($this);
		}

		return $this;
	}

	public function removeActivityCatCharge(ActivityCatCharge $activityCatCharge): self
	{
		if ($this->activityCatCharge->removeElement($activityCatCharge)) {
			// set the owning side to null (unless already changed)
			if ($activityCatCharge->getTmSavings() === $this) {
				$activityCatCharge->setTmSavings(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, TaskCatCharge>
	 */
	public function getTasksCatCharge(): Collection
	{
		return $this->tasksCatCharge;
	}

	public function addTasksCatCharge(TaskCatCharge $tasksCatCharge): self
	{
		if (!$this->tasksCatCharge->contains($tasksCatCharge)) {
			$this->tasksCatCharge[] = $tasksCatCharge;
			$tasksCatCharge->setTmSavings($this);
		}

		return $this;
	}

	public function removeTasksCatCharge(TaskCatCharge $tasksCatCharge): self
	{
		if ($this->tasksCatCharge->removeElement($tasksCatCharge)) {
			// set the owning side to null (unless already changed)
			if ($tasksCatCharge->getTmSavings() === $this) {
				$tasksCatCharge->setTmSavings(null);
			}
		}

		return $this;
	}
}
