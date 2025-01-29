<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'xtm_analytics_project_step')]
#[ORM\Entity]
class AnalyticsProjectStep implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'analytic_project_step_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'ordinal', type: 'smallint', nullable: false)]
	private int $ordinal;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\ManyToOne(targetEntity: AnalyticsProject::class, inversedBy: 'analyticsProjectSteps')]
	#[ORM\JoinColumn(name: 'analytics_project_id', referencedColumnName: 'id', nullable: false)]
	private AnalyticsProject $analyticsProject;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'xtrf_language_id', referencedColumnName: 'xtrf_language_id', nullable: false)]
	private XtrfLanguage $targetLanguageTag;

	#[ORM\Column(name: 'language_code', type: 'string', length: 30, nullable: false)]
	private string $targetLanguageCode;

	#[ORM\OneToMany(targetEntity: Statistics::class, mappedBy: 'step', orphanRemoval: true)]
	private mixed $statistics;

	public function __construct()
	{
		$this->statistics = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getOrdinal(): ?int
	{
		return $this->ordinal;
	}

	/**
	 * @return mixed
	 */
	public function setOrdinal(int $ordinal): self
	{
		$this->ordinal = $ordinal;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getAnalyticsProject(): ?AnalyticsProject
	{
		return $this->analyticsProject;
	}

	/**
	 * @return mixed
	 */
	public function setAnalyticsProject(?AnalyticsProject $analyticsProject): self
	{
		$this->analyticsProject = $analyticsProject;

		return $this;
	}

	public function getStatistics(): Collection
	{
		return $this->statistics;
	}

	/**
	 * @return mixed
	 */
	public function addStatistic(Statistics $statistic): self
	{
		if (!$this->statistics->contains($statistic)) {
			$this->statistics[] = $statistic;
			$statistic->setStep($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeStatistic(Statistics $statistic): self
	{
		if ($this->statistics->contains($statistic)) {
			$this->statistics->removeElement($statistic);
			// set the owning side to null (unless already changed)
			if ($statistic->getStep() === $this) {
				$statistic->setStep(null);
			}
		}

		return $this;
	}

	public function getTargetLanguageCode(): ?string
	{
		return $this->targetLanguageCode;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguageCode(string $targetLanguageCode): self
	{
		$this->targetLanguageCode = $targetLanguageCode;

		return $this;
	}

	public function getTargetLanguageTag(): ?XtrfLanguage
	{
		return $this->targetLanguageTag;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguageTag(?XtrfLanguage $targetLanguageTag): self
	{
		$this->targetLanguageTag = $targetLanguageTag;

		return $this;
	}
}
